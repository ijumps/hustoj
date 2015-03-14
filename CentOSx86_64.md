# Introduction #
  * 安装Hustoj参考Wiki中[CentOS](CentOS.md)节
  * CentOS 5.x默认php和mysql版本较低，建议升级到php 5.3.6和mysql 5.5，参考http://www.docin.com/p-188652804.html 这篇文章
  * 如果需要使用C#，请使用yum install mono-core glibc
  * 如果需要使用Pascal， 请使用yum install fpc
  * 如果需要python,perl,ruby等支持，请使用yum install安装相应的软件
  * 如果需要Java支持，请使用yum install java-1.6.0-openjdk java-1.6.0-openjdk-devel，建议不要使用官方的jdk，已证实不可用。
  * 修改core/judge\_client/judge\_client.cc文件，大约985行左右，参考修改代码如下（请注意修改对应的函数）：
<pre>
void copy_shell_runtime(char * work_dir) {<br>
execute_cmd("mkdir %s/lib", work_dir);<br>
execute_cmd("mkdir %s/lib64", work_dir);<br>
execute_cmd("mkdir %s/bin", work_dir);<br>
execute_cmd("cp /lib/* %s/lib/", work_dir);<br>
execute_cmd("cp /lib64/* %s/lib64/", work_dir);<br>
execute_cmd("cp -a /lib32 %s/", work_dir);<br>
execute_cmd("cp /bin/busybox %s/bin/", work_dir);<br>
execute_cmd("ln -s /bin/busybox %s/bin/sh", work_dir);<br>
execute_cmd("cp /bin/bash %s/bin/bash", work_dir);<br>
}<br>
void copy_ruby_runtime(char * work_dir) {<br>
<br>
copy_shell_runtime(work_dir);<br>
execute_cmd("mkdir %s/usr", work_dir);<br>
execute_cmd("mkdir %s/usr/lib64", work_dir);<br>
execute_cmd("cp /usr/lib64/libruby* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/bin/ruby* %s/", work_dir);<br>
<br>
}<br>
void copy_python_runtime(char * work_dir) {<br>
<br>
copy_shell_runtime(work_dir);<br>
execute_cmd("mkdir %s/usr", work_dir);<br>
execute_cmd("mkdir %s/usr/lib64", work_dir);<br>
execute_cmd("cp /usr/bin/python* %s/", work_dir);<br>
execute_cmd("cp /usr/lib64/libpython* %s/usr/lib64/", work_dir);<br>
<br>
}<br>
void copy_php_runtime(char * work_dir) {<br>
copy_shell_runtime(work_dir);<br>
execute_cmd("mkdir %s/usr", work_dir);<br>
execute_cmd("mkdir %s/usr/lib64", work_dir);<br>
execute_cmd("cp /usr/lib64/libedit* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libdb* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libgssapi_krb5* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libkrb5* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libk5crypto* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libxml2* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libncurses* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libgmp* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libbz2* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/lib64/libz.so* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/bin/php* %s/", work_dir);<br>
execute_cmd("chmod +rx %s/Main.php", work_dir);<br>
}<br>
void copy_perl_runtime(char * work_dir) {<br>
<br>
copy_shell_runtime(work_dir);<br>
execute_cmd("mkdir %s/usr", work_dir);<br>
execute_cmd("mkdir %s/usr/lib64", work_dir);<br>
execute_cmd("cp /usr/lib64/libperl* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /usr/bin/perl* %s/", work_dir);<br>
<br>
}<br>
void copy_mono_runtime(char * work_dir) {<br>
<br>
copy_shell_runtime(work_dir);<br>
execute_cmd("mkdir %s/usr", work_dir);<br>
execute_cmd("mkdir %s/proc", work_dir);<br>
execute_cmd("mkdir -p %s/usr/lib64/mono/2.0", work_dir);<br>
<br>
execute_cmd("cp -a /usr/lib64/mono %s/usr/lib64/", work_dir);<br>
<br>
execute_cmd("cp /usr/lib64/libgthread* %s/usr/lib64/", work_dir);<br>
<br>
execute_cmd("mount -o bind /proc %s/proc", work_dir);<br>
execute_cmd("cp /usr/bin/mono* %s/", work_dir);<br>
<br>
execute_cmd("cp /usr/lib64/libgthread* %s/usr/lib64/", work_dir);<br>
execute_cmd("cp /lib/libglib* %s/lib/", work_dir);<br>
execute_cmd("cp /lib/tls/i686/cmov/lib* %s/lib/tls/i686/cmov/", work_dir);<br>
execute_cmd("cp /lib/libpcre* %s/lib/", work_dir);<br>
execute_cmd("cp /lib/ld-linux* %s/lib/", work_dir);<br>
execute_cmd("cp /lib64/ld-linux* %s/lib64/", work_dir);<br>
execute_cmd("mkdir -p %s/home/judge", work_dir);<br>
execute_cmd("chown judge %s/home/judge", work_dir);<br>
execute_cmd("mkdir -p %s/etc", work_dir);<br>
execute_cmd("grep judge /etc/passwd>%s/etc/passwd", work_dir);<br>
}<br>
</pre>