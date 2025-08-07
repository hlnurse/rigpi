#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/ioctl.h>
#include <linux/serial.h>
#include <string.h>

int main(int argc, char* argv[]) {
	const char *device = "/dev/ttyUSB0"; // Replace with your serial device
	int fd;
	const char* str=argv[1];
	// Open the serial port
	fd = open(device, O_RDWR | O_NOCTTY);
	if (fd < 0) {
		perror("open");
		return 1;
	}
	printf("%c\n", str);
	const char o = '1';
//	argv[1]=ok;
	if (strchr(str,o)){
		// Set DTR (Data Terminal Ready) line
		int dtr_flag = TIOCM_DTR;
	
		// Assert DTR (set high)
		if (ioctl(fd, TIOCMBIS, &dtr_flag) < 0) {
			perror("ioctl assert DTR");
			close(fd);
			return 1;
		} else {
			printf("DTR set high\n");
		}
	
	}else{
		// Clear DTR (set low)
		int dtr_flag = TIOCM_DTR;
		if (ioctl(fd, TIOCMBIC, &dtr_flag) < 0) {
			perror("ioctl clear DTR");
			close(fd);
			return 1;
		} else {
			printf("DTR set low\n");
		}
	}
	// Close the serial port
	close(fd);

	return 0;
}
