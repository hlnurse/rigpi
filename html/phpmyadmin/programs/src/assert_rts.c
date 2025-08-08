#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/ioctl.h>
#include <linux/serial.h>
#include <linux/tty.h>

int main(int argc, char *argv[]) {
	if (argc != 2) {
		fprintf(stderr, "Usage: %s <device>\n", argv[0]);
		return 1;
	}
	int res;
	// This will execute the LS command in the current directory, 
	//     unfortunately the result is just an exit code.
	res = system("stty -F device crtscts");
	const char *device = argv[1];
	int fd = open(device, O_RDWR | O_NOCTTY);
	if (fd == -1) {
		perror("open");
		return 1;
	}

	int status;
	if (ioctl(fd, TIOCMGET, &status) == -1) {
		perror("ioctl TIOCMGET");
		close(fd);
		return 1;
	}

	status |= TIOCM_RTS;

	if (ioctl(fd, TIOCMSET, &status) == -1) {
		perror("ioctl TIOCMSET");
		close(fd);
		return 1;
	}

	close(fd);
	return 0;
}
