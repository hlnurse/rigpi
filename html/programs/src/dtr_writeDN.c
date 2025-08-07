#include <stdio.h>
#include <fcntl.h>
#include <unistd.h>
#include <termios.h>
#include <sys/ioctl.h>

int main() {
	const char* serialDevice ="/dev/ttyUSB0";// "/dev/serial/by-id/usb-Silicon_Labs_CP2102_USB_to_UART_Bridge_Controller_IC-7300_02020433-if00-port0"; // Replace with your actual serial port device
	int fd = open(serialDevice, O_RDWR);
	if (fd == -1) {
		perror("Error opening serial port");
		return 1;
	}

	// Set DTR to ON
	int dtrState = TIOCM_DTR;
	if (ioctl(fd, TIOCMBIS, &dtrState) == -1) {
		perror("Error setting DTR state");
		close(fd);
		return 1;
	}

	printf("DTR set to %i\n",TIOCMBIS);
sleep(2);
printf("now clearing RTS\n");
	if (ioctl(fd, TIOCMBIC, &dtrState) == -1) {
	perror("Error setting DTR state");
	close(fd);
	return 1;
}
printf("waiting 2 sec\n");

sleep(2);

	close(fd);
	return 0;
}