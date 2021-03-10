# 2. Composing some structure

## 2.1 Docker compose

So in chapter one you've started your first container.
Sure, you can do it this way for every container that you would need, but what if ~~someone~~ something did that for you? 
Wouldn't that be amazing!? 

Good thing that with DOCKER COMPOSE you can :D

Docker compose is a tool that allows you to write down what kind of infrastructure you need, and start it all in one go.

A basic Docker compose file would look a bit like this

```yaml
services:
  php:
    container_name: myproject_php # Name of the container, for communication
    image: php:7.4-apache         # Which docker image we want to use
    ports:
      - '8080:80'                 # Which port we want the apache server to listen on
    volumes:
      - './php:/var/www/html'     # Which directory we store our code, and where it needs to go

  mysql:
    container_name: myproject_mysql
    image: mysql:5.7
    volumes:
      - './mysql:/var/lib/mysql'
    ports:
      - 18766:3306
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: myproject
      MYSQL_USER: myproject_user
      MYSQL_PASSWORD: myproject_password
```

And there you have it. One webserver that can run your PHP files, and one database :)
You might even realise that most of it (except for the database) looks quite similar to the command line option we showed in the previous chapter.

| Command line                                                  | Compose file                   |
| ------------------------------------------------------------- |------------------------------- |
| -p 8080:80                                                    | ports: - "8080:80"             |
| --mount type=bind,source="$(pwd)"/php,target=/var/www/html    | volumes: - ./php:/var/www/html |
| php:7.4-apache                                                | image: php:7.4-apache          |

It just looks a bit nicer, and a lot easier to read/maintain.

---

## 2.2 Actually starting the infrastructure

The example above can basically be copied without much changes into the root of your project (to be).
Create a `docker-compose.yaml` file and paste the contents into it.

And that it!

No... Wait... That's having the infrastructure in your project.
Now open your terminal and make sure you are in the same directory as the just created yaml file.
And run the following

```shell
docker-compose up
```

You will be greeted with a whole wall of logs, directly from your freshly started containers!
And even better, you are now also able to open your browser and go to `http://localhost:8080` to visit the same page you created in chapter one!!


## 2.3 Would you be quiet!?

Having all those logs flooding your terminal might be a bit too much noise.
Good thing we can also tell docker-compose to run as a daemon in the background!

We do this by adding the `-d` flag to the command above.

```shell
docker-compose up -d
```

Which should greet you with a much more simple overview of the containers that have been started.
```shell
Starting docker-symfony-tutorial_mysql_1 ... done
Starting docker-symfony-tutorial_php_1   ... done
```

Want some extra confirmation that your containers are running?
There's a command for that!

```shell
docker ps
```

This will show an overview of all the active containers :D

```shell
CONTAINER ID   IMAGE            COMMAND                  CREATED          STATUS          PORTS                                NAMES
3539ccbee9a6   mysql:5.7        "docker-entrypoint.s…"   14 seconds ago   Up 13 seconds   33060/tcp, 0.0.0.0:18766->3306/tcp   myproject_mysql
b74796be63a8   php:7.4-apache   "docker-php-entrypoi…"   14 seconds ago   Up 13 seconds   0.0.0.0:8080->80/tcp                 myproject_php
```

## 2.4 All your database are belong to us!

Now that we also have a functioning database, we might as well use it!

Still got that index.php file? Good! Let's create a database connection in there!
Throw the following in there:

```php
try {
    $dbh = new PDO('mysql:host=myproject_mysql;dbname=myproject', 'myproject_user', 'myproject_password');

    print 'Connection established!';
} catch (PDOException $e) {
    print 'Error!: ' . $e->getMessage() . '<br/>';
}
```

This will try to make a connection to the database, using the container name (`myproject_mysql`) as a host, and the credentials provided through the docker-compose file.

Upon refreshing the page, you will be greeted with a very nice error message, stating that the driver could not be found D:

```text
Error!: could not find driver
```

Okay... That's a bummer... But we can fix that!
As you might have heard it at some point in your life, if you want to get things done properly, you might as well do it yourself!

## 2.5 Dockerfiles for all our (current) problems.

Okay, so the nice thing about Docker images, is that we can also expand on them.
We do this by creating our own Dockerfile and building this on top of the php image that we are currently using.

Let's create a `docker` directory in our project.
And in there a `php` sub-directory.

In here, we are going to create our own image that _does_ allow us to connect to our database!
We do that by creating a file named... `Dockerfile`... Whatever creative genius came up with that name...
And in that `Dockerfile` we are going to put the following:

```Dockerfile
FROM php:7.4-apache

RUN docker-php-ext-install pdo pdo_mysql
```

The first line defines the base image that we want to use.
The second line will install the correct drivers we need.

In order to use our own image, we will have to update our `docker-compose.yaml` file as well!

Just replace `image: php:7.4-apache` with `build: ./docker/php` and you're set.

Now all that is left, is to restart our Docker containers, and refresh the browser!

```shell
docker-compose down
docker-compose up -d
```

You will notice that the `docker-compose up -d` will now take a bit longer, as it needs to create a new image that we can use.
After that though, you can refresh the browser, and should see the following message:

```text
Connection established!
```

That's it! You can now build your entire php application, with a working database!

---

This concludes the second part of the Docker and Symfony tutorial. Feel free to play around with what you've learnt so far and see if you can create a small php application that actually uses the database!
The content of this chapter might be a bit intimidating, but don't worry... It will only get worse/better from here!

---

More reading material:
- [Getting started with Docker compose](https://docs.docker.com/compose/gettingstarted/)
- [Working with the PDO library](https://phpdelusions.net/pdo)
- [Dockerfile best practices](https://docs.docker.com/develop/develop-images/dockerfile_best-practices/)
