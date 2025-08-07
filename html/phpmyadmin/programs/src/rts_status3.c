#include <stdio.h>
#include <fcntl.h>
#include <sys/ioctl.h>
#include <linux/serial.h>
#include <unistd.h>
#include <stdlib.h>
#include <string.h>
#include <termios.h>

// Define the callback function type
typedef void (*ResultCallback)(int result);

// Function that calculates squares and uses a callback to return results
void monCTS(int fd, ResultCallback callback) {
	int status;
	int status1;
	int i;
	int j;
	while(1) {
//	printf("waiting\n");
	status=TIOCM_CTS;
		ioctl(fd, TIOCMGET, &status1);
	printf("in monCTS, status is %i\n", status1);

		ioctl(fd, TIOCMIWAIT, &status);
		ioctl(fd, TIOCMGET, &status1);
		printf("before if, status is %i\n", status1);

			if (status & TIOCM_CTS) {
 				j=3;
				printf("3\n");
				fflush(stdout);
//				break;
			} else {
//				j=4;
				printf("4\n");
				fflush(stdout);
//				break;
			}
//		}
//		break;
	}
}

// Callback function implementation to handle results
void handleResult(int result) {

	printf("%d\n",result);
	fflush(stdout);
	usleep(500);
//	return;
}

int main() {
//printf("%d\n",0);
//return 0;
	int n = 10;
	char str[80];
	char *dev;
	int fd;
	int status;
	dev="/dev/ttyUSB0";
//	printf ("opening...");
	fd = open(dev, O_RDWR | O_NOCTTY);
	if (fd == -1) {
		perror("open");
		return 1;
	}
/*	strcpy(str,"stty -F ");
	strcat(str,dev);
	strcat(str," crtscts");
	status=system(str);
*/	status=TIOCM_DTR;
	ioctl(fd, TIOCMGET, &status);
	printf("before, status is %i\n", status);
	int status2;
	ioctl(fd, TIOCMGET, &status2);
	status2 &= ~TIOCM_DTR;
	ioctl(fd, TIOCMSET, &status2);
	int dtrState=TIOCM_DTR;
/*		if (ioctl(fd, TIOCMBIC, &dtrState) == -1) {
		printf("error");
//		perror("set_DTR(): TIOCMSET");
		return -1;
	}
*/
	ioctl(fd, TIOCMGET,&status);
printf("here");
	printf("after, status is %i\n", status & TIOCM_CTS);
	// Call the function with a callback
	printf( "calling monCTS with fd %i\n",fd);////////////////
	monCTS(fd, handleResult);

	return 0;
}
