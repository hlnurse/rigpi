#include <stdio.h>
#include <sys/types.h>
#include <termios.h>
#include <sys/ioctl.h>
#include <fcntl.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <stdio.h>
#include <unistd.h>

#define SERIAL_DEVICE	"/dev/ttyUSB0"
int set_DTR(int fd, unsigned short level)
{
	int status;

	if (fd < 0) {
		perror("Set_DTR(): Invalid File descriptor");
		return -1;
	}

	if (ioctl(fd, TIOCMGET, &status) == -1) {
		perror("set_DTR(): TIOCMGET");
		return -1;
	}

	if (level) 
		status |= TIOCM_DTR;
	else 
		status &= ~TIOCM_DTR;

	if (ioctl(fd, TIOCMSET, &status) == -1) {
		perror("set_DTR(): TIOCMSET");
		return -1;
	}
	return 0;

}

int set_RTS(int fd, unsigned short level)
{
	int status;

	if (fd < 0) {
		perror("Invalid File descriptor");
		return -1;
	}

	if (ioctl(fd, TIOCMGET, &status) == -1) {
		perror("set_RTS(): TIOCMGET");
		return -1;
	}

	if (level) 
		status |= TIOCM_RTS;
	else 
		status &= ~TIOCM_RTS;

	if (ioctl(fd, TIOCMSET, &status) == -1) {
		perror("set_RTS(): TIOCMSET");
		return -1;
	}
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
	char str[80];
	int status;
	strcpy(str,"stty -F ");
	strcat(str,SERIAL_DEVICE);
	strcat(str," crtscts");
	status=system(str);
	while(1){
		ioctl(fd, TIOCMIWAIT, TIOCM_CTS);
	//printf("next");
		ioctl(fd, TIOCMGET, &serial);
	//	printf("%d\n",serial);
		if (serial & TIOCM_CTS){
			printf("3\n");
			return(0);
		}else{
			printf("4\n");
			return(0);
		}
	}
}