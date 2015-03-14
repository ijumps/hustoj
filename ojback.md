# HUSTOJ评测后台的机制 #

## 内存 ##
1、页错误方式
Linux在每次给进程分配新的内存时，会触发"页错误"，并进行计数。这个计数可以用wait的返回值struct rusage `*` ruse中的.ru\_minflt字段读取到，页的大小可以用getpagesize得到，通常为4k,因此内存通常为4k的整数倍。
相关代码在get\_page\_fault\_mem函数中。
因为Linux的内存写时分配机制，用这种方式计算的内存不包含未经写入的内存，因此更加宽松，在早期版本中均使用这个方式，在最新代码中只对Java使用这种方式。

搜索关键词：wait rusage

manpage:man 2 wait

2、proc文件系统
文件系统中的/proc目录不是放置在磁盘中的实际文件，而是整个系统运行状态的文件形式的接口。其中/proc/数字/目录下放置着相对应进程号的进程信息。在get\_proc\_status函数中，读取其中的内容可以得到称之为VmSize的内存信息。这个大小是进程试图获取的内存，不管是否真的写入使用，因此比页错误方式更严格，可以限制c语言使用者随意申请超大数组的行为。目前Java以外的语言都使用这种方式计算内存。JVM会在刚启动时尝试跟宿主系统预约大量内存以备用，因此会遇到大量不同程序都是使用完全相同内存大小的情况，只能以页错误方式处理。

搜索关键词：proc文件系统

manpage:man 5 proc


## 时间 ##
与内存中的页错误计数类似，wait返回的ruse结构中还保含ru\_utime和ru\_stime两个字段，分别对应进程消耗的用户级时间和系统级时间。
时间的精度跟Linux内核设定的tick大小有关，通常最小单位4ms。

谷歌百度关键词: linux HZ Tick Jiffies

manpage: tickadj http://linux.die.net/man/8/tickadj

相关代码在约1692行
```
usedtime += (ruse.ru_utime.tv_sec * 1000 + ruse.ru_utime.tv_usec / 1000);
usedtime += (ruse.ru_stime.tv_sec * 1000 + ruse.ru_stime.tv_usec / 1000);
```


## 安全 ##
下面的内容主要是讲了OJ后台的安全机制
### chroot ###
所有用户程序被放置在runX目录下运行，并进行chroot.
细心的用户会发现Java没有做chroot，不是不能做，而是考虑到java已经有policy机制，并且jvm的大小太大，平衡考虑没有chroot。
有很多copy\_XXX\_runtime函数，他们的用途是给chroot环境准备必须的运行库和程序。

manpage:man 8 chroot

### setuid ###
setuid setgid setresuid等函数可以切换为judge用户的身份，通过身份限制其权限。

因为单个用户进程数量限制，setuid可能失败，因此需要检查返回值。

```
while(setgid(1536)!=0) sleep(1);
while(setuid(1536)!=0) sleep(1);
while(setresuid(1536, 1536, 1536)!=0) sleep(1);
```
同时使用
```
iptables -A OUTPUT -m owner --uid-owner judge -j DROP
```
可以限制网络通信，避免可能的信息泄露。

### rlimit ###
再调用用户程序覆盖进程前，先用setrlimit设定最大内存限制，为了不致用户争议，通常会允许多给一些最后再判MLE。
CPU时间、文件大小等类似。

manpage:man 2 setrlimit

### java policy ###
Java有原生的安全机制，用一个java.policy文件定义黑白名单。
hustoj用的这个文件在/home/etc/judge0.policy

搜索关键词：java.policy


### 非法调用限制 ###
在UNIX中有上百个[系统调用](http://www.ibm.com/developerworks/cn/linux/kernel/syscall/part1/appendix.html)，有一大部分是在用户程序运行过程中不需要的，比如说mkdir,mount等，还有一部分会对系统造成安全隐患的，比如fork,kill,exec等，还有一些比如socket等会造成敏感信息，比如测试数据的泄漏等。
因为以上情况的存在，所以需要在运行用户程序的时候对用户加以限制，linux下的ptrace在这里是一个非常好用的工具，它可以在用户态和内核态之间切换之前和之后，将进程暂停，以方便控制进程的处理，控制进程通过ptrace可以读取到当前进程想要去做什么，这样就可以在用户程序造成破坏之前将程序中止。
限制非法系统调用，最好的办法是使用白名单机制，只允许程序使用一个小集合里的调用，对于其它调用，即使它是安全的，也不会被允许，比如mkdir。
由于Pascal,Java,C/C++的机制有些区别，因此，三种不同语言的白名单各不相同。
```
ptrace(PTRACE_GETREGS, pidApp, NULL, &reg);
```
这里的reg是对系统调用执行之前，寄存器状态的一个复制品。从中可以取得系统调用的编号，用于白名单验证。

manpage: ptrace


相关代码见http://code.google.com/p/hustoj/source/browse/trunk/core/judge_client/okcalls.h#3

---
