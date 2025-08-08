#include <stdio.h>
#include <sys/types.h>
#include <termios.h>
#include <sys/ioctl.h>
#include <fcntl.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <unistd.h>


#define SERIAL_DEVICE	"/dev/ttyUSB0"
int set_DTR(unsigned short level)
{
	int status;
	const char* serialDevice = SERIAL_DEVICE; // Replace with your actual serial port device
	int fd = open(serialDevice, O_RDWR | O_NOCTTY);

	if (fd < 0) {
		perror("Set_DTR(): Invalid File descriptor");
		return -1;
	}

	if (ioctl(fd, TIOCMGET, &status) == -1) {
		perror("set_DTR(): TIOCMGET");
		return -1;
	}
	printf("level is %i\n",level);
	int dtrState = TIOCM_DTR;
	if (level==1){ 
		if (ioctl(fd, TIOCMBIS, &dtrState) == -1) {
			perror("set_DTR(): TIOCMSET");
			return -1;
		}
		printf("BIS\n");
	}else{ 
		if (ioctl(fd, TIOCMBIC, &dtrState) == -1) {
		perror("set_DTR(): TIOCMSET");
		return -1;
		printf("BIC\n");
		}
}
	sleep(2);
	return 0;

}

int main()
{
	int fd;
	int retval;
	int serial;

	fd = open(SERIAL_DEVICE, O_RDWR);
	if (fd < 0) {
		perror("Failed to open SERIAL_DEVICE");
		exit(1);
	}
	
	retval = ioctl(fd, TIOCMGET, &serial);
	if (retval < 0) {
		perror("ioctl() failed");
		exit(0);
	}

	if (serial & TIOCM_DTR){
		printf("now set_DTR\n");

		printf("%s:DTR is set\n", SERIAL_DEVICE);
	}else{
		printf("%s:DTR is not set\n", SERIAL_DEVICE);
	}
	int level=0;
	printf("setting dtr\n");
	set_DTR(1);
	printf("dtr set, now sleep\n");
	sleep(5);
	set_DTR(0);
	printf("dtr unset\n");
	retval = ioctl(fd, TIOCMGET, &serial);

	if (serial & TIOCM_DTR)
			printf("%s: (after set) DTR is set\n", SERIAL_DEVICE);
		else
			printf("%s: (after set) DTR is NOT set\n", SERIAL_DEVICE);
	sleep(5);
	return 0;
}