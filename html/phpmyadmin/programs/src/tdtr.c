#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <fcntl.h>
#include <sys/ioctl.h>
#include <linux/serial.h>
#include <time.h>

int main() {
	int fd = open("/dev/ttyUSB0", O_RDWR | O_NOCTTY);
	if (fd == -1) {
		perror("open");
		exit(EXIT_FAILURE);
	}

	int modem_lines = TIOCM_DTR;  // Set RTS line

	if (ioctl(fd, TIOCMBIS, &modem_lines) == -1) {
		perror("ioctl");
		close(fd);
		exit(EXIT_FAILURE);
	}

	// Modem control line has been set
	printf("DTR line set successfully\n");
	sleep(2);
		close(fd);
		return 0;
	}
