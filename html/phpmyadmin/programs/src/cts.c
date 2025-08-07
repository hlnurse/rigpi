// ioctl_lib.c
#include <stdio.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/ioctl.h>
#include <string.h>
#include <stdlib.h>

//#include <linux/termios.h>
int main(int argc, char *argv[]) {
	char str[80];
	int status;

//int get_modem_status(const char *device) {
	int fd = open(argv[1], O_RDWR | O_NOCTTY);
	if (fd < 0) {
		perror("open");
		return -1;
	}
	strcpy(str,"stty -F ");
	strcat(str,argv[1]);
	strcat(str," crtscts");
	status=system(str);

	if (ioctl(fd, TIOCMGET, &status) < 0) {
		perror("ioctl TIOCMGET");
		close(fd);
		return -1;
	}
	if (status & TIOCM_DTR) {
		printf("3\n");
	} else {
		printf("4\n");
	}
sleep(2);
	close(fd);
	return status;
}
