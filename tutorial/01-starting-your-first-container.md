# 1. Starting your first Docker container

## 1.1 Hello world!

We all need to start somewhere with Docker containers. And what better than to do a very simple "Hello world" example?

```shell
docker run hello-world
```

Yes, it's that simple!

You should now see an output in your terminal that starts like this:
```shell
Hello from Docker!
This message shows that your installation appears to be working correctly.

To generate this message, Docker took the following steps:
 1. The Docker client contacted the Docker daemon.
 2. The Docker daemon pulled the "hello-world" image from the Docker Hub.
    (amd64)
 3. The Docker daemon created a new container from that image which runs the
    executable that produces the output you are currently reading.
 4. The Docker daemon streamed that output to the Docker client, which sent it
    to your terminal.
```

## 1.2 What about PHP?

Fair, you can't really run an application in a "Hello World" container. So let's start a PHP container!

```shell
docker run php:7.4-apache

```

The first time you run this, Docker will have to check if the image we want to use (``php:7.4-apache``) already exists.
If it does not, then Docker will attempt to download the image so that we can use it.
Eventually we should end up with the following 2 lines in our terminal:

```shell
[Tue Jan 05 20:17:24.341219 2021] [mpm_prefork:notice] [pid 1] AH00163: Apache/2.4.38 (Debian) PHP/7.4.13 configured -- resuming normal operations
[Tue Jan 05 20:17:24.341262 2021] [core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'
```

Awesome! We now have a PHP server running! That... We can't... use......
Okay, obviously, we are missing a few things here:

- How does it know what php files to use?
- How can we access this in our browser?
- How can we do _anything_, really?

## 1.3 PHP Hello world

Right, let's start with having our PHP server approachable in our browser!
In order to do this, we will have to tell Docker what port from our host (computer/laptop/whatever you are working on), needs to be forwarded to the container.
This can be done by running the same ``docker run`` command as before, but now with the ``-p`` flag added to it.

```shell
docker run -p 8080:80 php:7.4-apache
```

This tells Docker that your container should now forward all traffic from port 8080 on the host, to 80 into the container.
The reason we use ports 8080 in this tutorial is because PHP-apache listens for incoming connections on port 80 by default, but this could be conflicting with other things you might have running in the background.
So for the ``-p`` flag, you always read it from left to right:

```shell
docker run -p [port on host]:[port in container] php:7.4-apache
```

And if you now open your browser of choice, and go to http://localhost:8080, you will be welcomed with a... forbidden response...
That's because we haven't told the container what php files to run for us!

So let's create a simple "Hello world" php file and make sure that our Docker container will show it to us in the browser.

Create a ``php`` directory and make a ``index.php`` file with the following contents:

```php
<?php
echo '<h1>Hello world!</h1>';
echo phpinfo();
```

Now we need to update our Docker run command and let it know where to look for our PHP files!

```shell
docker run -p 8080:80 --mount type=bind,source="$(pwd)"/php,target=/var/www/html php:7.4-apache
```

Deconstructing the ``--mount`` part a bit:
- type: What kind of mount do we want to create? (can be bind, volume or tempfs)
- source: Which directory's contents on our host should be sent to the container? (points to the default directory that handles web traffic)
- target: What directory within the container should receive our files? ("$(pwd)" points to the directory your terminal is currently in)

If you now reload your browser, you should see the contents of your index.php file!
A nice "Hello world" message, followed by the php information from the ``phpinfo()`` function!

---

This concludes the first part of the Docker and Symfony tutorial. Feel free to play around with what you've learnt so far and see if you can create a small php application within the ``/php`` directory we created above.
You will see that you don't have to run the ``docker run`` command every time, since our version of the files are the same as what the container uses!

---

More reading material:
- [Getting started with Docker](https://docs.docker.com/get-started/)
- [Docker PHP images](https://hub.docker.com/_/php)
