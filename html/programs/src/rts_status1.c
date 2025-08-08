#include <stdio.h>
#include <fcntl.h>
#include <sys/ioctl.h>
#include <linux/serial.h>
#include <unistd.h>
#include <stdlib.h>
#include <string.h>
#include <termios.h>


#define SERIAL_DEVICE	"/dev/ttyUSB0"

// Define the callback function type
typedef void (*ResultCallback)(int result);
int set_DTR(int fd, unsigned short level)
{
	int status;
//	const char* serialDevice = SERIAL_DEVICE; // Replace with your actual serial port device
//	int fd = open(serialDevice, O_RDWR | O_NOCTTY);
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
		}
		printf("BIC\n");

	}
}

void monCTS(int fd, ResultCallback callback) {
	int status=-1;
	int ctsState=TIOCM_CTS;
	int i;
	int j;
	while(1) {
//		printf("here\n");
		ioctl(fd, TIOCMGET, &status);
		printf("here is status %i\n",status & ctsState);

		ioctl(fd, TIOCMIWAIT, &ctsState);
		ioctl(fd, TIOCMGET, &status);

			if (status & ctsState) {
				printf("3\n");
				fflush(stdout);
			} else {
				printf("4\n");
				fflush(stdout);
			}
//		}
	}
}

// Callback function implementation to handle results
void handleResult(int result) {

	printf("%d\n",result);
	fflush(stdout);
	usleep(500);
//	return;
}

int main(int argc, char *argv[]) {
	int n = 10;
	int fd;
	int status;
	char str[80];
	int dtrState = TIOCM_DTR;
	int ctsState=TIOCM_CTS;

	const char *device1=argv[1];//"/dev/serial/by-id/usb-Silicon_Labs_CP2102_USB_to_UART_Bridge_Controller_IC-7300_02020433-if00-port0";
		fd = open(device1, O_RDWR | O_NOCTTY);

		strcpy(str,"stty -F ");
		strcat(str,argv[1]);
		strcat(str," -crtscts");
		status=system(str);

		if (ioctl(fd, TIOCMBIC, &dtrState) == -1) {
			perror("set_DTR(): TIOCMSET");
			return -1;
		}
/*		if (ioctl(fd, TIOCMBIC, &ctsState) == -1) {
			perror("set_DTR(): TIOCMSET");
			return -1;
		}
*/
//		set_DTR(fd,0);

//////////////////////		if (device == -1) {
//			std::cerr << "Error " << errno << " opening device " << std::strerror(errno) << std::endl;
///			return 1;
//		}
//sleep(1);
//		status = TIOCM_DTR;
		   
//		ioctl(fd, TIOCMBIC, &status);
		printf("done set up\n");
//sleep(2);	
//		close(device1);
//		return 0;
//		}


/*	const char *device=argv[1];//"/dev/serial/by-id/usb-Silicon_Labs_CP2102_USB_to_UART_Bridge_Controller_IC-7300_02020433-if00-port0";
	printf("device: %s\n",device);
	// Open the serial port
	fd = open(device, O_RDWR | O_NOCTTY | O_NDELAY);
	if (fd == -1) {
		perror("Unable to open serial port");
		return EXIT_FAILURE;
	}
	printf("fd %i\n", fd);
	strcpy(str,"stty -F ");
	strcat(str,device);
	strcat(str," crtscts");
	status=system(str);
//	set_dtr(fd,0);
*/
	// Get the current options for the port
/*	printf("now clearing DTR\n");  //must clear DTR after opening port to prevent key down
		if (ioctl(fd, TIOCMBIC, &dtrState) == -1) {
		perror("Error setting DTR state");
		close(fd);
		return 1;
	}
*/	

	// Call the function with a callback
	printf("call monCTS\n");
//	monCTS(fd, handleResult);
//	int ctsState=TIOCM_CTS;

//		ioctl(fd, TIOCMGET, &ctsState);
//		status=0;
		printf("here is status %i\n",status & ctsState);

		ioctl(fd, TIOCMIWAIT, &ctsState);
		ioctl(fd, TIOCMGET, &status);

			if (status & ctsState) {
				printf("3\n");
				fflush(stdout);
			} else {
				printf("4\n");
				fflush(stdout);
			}
//		}

sleep(2);
close(fd);
	return 0;

}
