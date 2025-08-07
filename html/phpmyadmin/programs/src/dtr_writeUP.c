#include <stdio.h>
#include <fcntl.h>
#include <unistd.h>
#include <termios.h>
#include <sys/ioctl.h>

int main() {
	const char* serialDevice = "/dev/ttyUSB0"; // Replace with your actual serial port device
	int fd = open(serialDevice, O_RDWR | O_NOCTTY);
	if (fd == -1) {
		perror("Error opening serial port");
		return 1;
	}

	// Set DTR to ON
	int dtrState = TIOCM_DTR;
	if (ioctl(fd, TIOCMBIC, &dtrState) == -1) {
		perror("Error setting DTR state");
		close(fd);
		return 1;
	}

	printf("DTR set to OFF.\n");

	close(fd);
	return 0;
}