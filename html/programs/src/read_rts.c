#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/ioctl.h>
#include <linux/serial.h>
#include <linux/tty.h>
#include <string.h>

int main(int argc, char *argv[]) {
	if (argc != 2) {
		fprintf(stderr, "Usage: %s <device>\n", argv[0]);
		return 1;
	}

	const char *device = argv[1];
	int fd = open(device, O_RDONLY | O_NOCTTY);
	if (fd == -1) {
		perror("open");
		return 1;
	}
	char str[40];
	strcpy(str,"stty -F ");
	strcat(str,argv[1]);
	strcat(str," -crtscts");
	int status=system(str);
	int dtrState=TIOCM_DTR;
if (ioctl(fd, TIOCMBIC, &dtrState) == -1) {
	perror("set_DTR(): TIOCMSET");
	return -1;
	}

	int ctsState=TIOCM_CTS;
//	ioctl(fd, TIOCMIWAIT, &ctsState);
if (ioctl(fd, TIOCMGET, &status) == -1) {
		perror("ioctl");
		close(fd);
		return 1;
	}

	close(fd);

	if (status & TIOCM_CTS) {
		printf("CTS is ON\n");
	} else {
		printf("CTS is OFF\n");
	}

	return 0;
}
