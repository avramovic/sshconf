# sshconf

Small CLI utility to manage ssh config file on Linux/Mac

## Requirements

* wget
* PHP 7.1

## Installation

Copy and paste the following set of commands in your shell/terminal:

```
wget https://github.com/avramovic/sshconf/raw/master/sshconf.phar && mv sshconf.phar /usr/local/bin/sshconf && chmod +x /usr/local/bin/sshconf && echo "sshconf installed, type: sshconf" && test -e ~/.ssh/config && cp ~/.ssh/config ~/.ssh/config.sshconf.backup
```

This will download and copy the executable in `/usr/local/bin/sshconf` and will back up existing ssh config file if it exists.

## Commands

### ls

List all connections

```
sshconf ls
```

### add

Add a new SSH connection

```
sshconf add [options] [--] <host> <hostname>
```

### edit

Edit an existing SSH connection

```
sshconf edit [options] [--] <host>
```


### rm

Remmove an existing SSH connection

```
sshconf rm <host>
```
