SHELL = /bin/sh

all: clean build clean

clean:
	rm -rf *.zip wordpress-accessibility-plugin

build:
	mkdir wordpress-accessibility-plugin;
	cp -r includes wordpress-accessibility-plugin;
	cp -r userway.php wordpress-accessibility-plugin;
	cp -r readme.txt wordpress-accessibility-plugin;
	zip -r wordpress-accessibility-plugin.zip wordpress-accessibility-plugin;