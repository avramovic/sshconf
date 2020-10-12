# sshconf

Small CLI utility to manage ssh config file on Linux/Mac

## Requirements

* wget or cURL
* PHP 7.1

## Installation

Copy and paste the following set of commands in your shell/terminal:

```
wget https://github.com/avramovic/sshconf/releases/latest/download/sshconf.phar && mv sshconf.phar /usr/local/bin/sshconf && chmod +x /usr/local/bin/sshconf && echo "sshconf installed, type: sshconf" && test -e ~/.ssh/config && cp ~/.ssh/config ~/.ssh/config.sshconf.backup
```

No wget? Try with cURL:

```
curl -L https://github.com/avramovic/sshconf/releases/latest/download/sshconf.phar -o sshconf.phar && mv sshconf.phar /usr/local/bin/sshconf && chmod +x /usr/local/bin/sshconf && echo "sshconf installed, type: sshconf" && test -e ~/.ssh/config && cp ~/.ssh/config ~/.ssh/config.sshconf.backup
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

Remove an existing SSH connection

```
sshconf rm <host>
```

### view

View an existing SSH connection

```
sshconf view <host>
```
