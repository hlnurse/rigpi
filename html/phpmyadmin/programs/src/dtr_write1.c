#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <fcntl.h>
#include <sys/mman.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <string.h>
#include <termios.h>
#include <time.h>
#include <linux/serial.h>
#include <sys/ioctl.h>


#define SHARED_MEMORY_NAME "/shared_mem_example"
#define SHARED_MEMORY_SIZE sizeof(int)
	int fd;

void instance1(int fd) {
	// Create shared memory
	int shm_fd = shm_open(SHARED_MEMORY_NAME, O_CREAT | O_RDWR, 0666);
	ftruncate(shm_fd, SHARED_MEMORY_SIZE);
	int* shared_var = (int*)mmap(0, SHARED_MEMORY_SIZE, PROT_READ | PROT_WRITE, MAP_SHARED, shm_fd, 0);

	// Initialize shared variable
	*shared_var = 1;
	int serial;
	char str[20];
	char *device;
	//int fd;
	int status;
	device="/dev/ttyUSB2";

	int s;
//	const char* str=argv[1];
	// Open the serial port
	strcpy(str,"stty -F ");
	strcat(str,device);
	strcat(str," crtscts");
	status=system(str);
	printf("fd: %d\n", fd);
	ioctl(fd, TIOCMBIS, TIOCM_DTR);
	printf("ioctl: %d\n", status);
	while (*shared_var==1) {

		usleep(50);
		printf("here %d\n", *shared_var);

	}

	// Clean up
	munmap(shared_var, SHARED_MEMORY_SIZE);
	close(shm_fd);
	shm_unlink(SHARED_MEMORY_NAME);

}

void instance2(int fd) {
	// Open existing shared memory
	int shm_fd = shm_open(SHARED_MEMORY_NAME, O_RDWR, 0666);
	int* shared_var = (int*)mmap(0, SHARED_MEMORY_SIZE, PROT_READ | PROT_WRITE, MAP_SHARED, shm_fd, 0);

	// Modify shared variable
	*shared_var = 0;
	ioctl(fd, TIOCMBIC, TIOCM_DTR);
	// Clean up
	munmap(shared_var, SHARED_MEMORY_SIZE);
	close(shm_fd);
	shm_unlink(SHARED_MEMORY_NAME);
}

int main(int argc, char *argv[]) {
	const char* device = "/dev/serial/by-id/usb-Silicon_Labs_CP2102_USB_to_UART_Bridge_Controller_IC-7300_02020433-if00-port0"; // Replace with your serial device
	int instance_number = atoi(argv[1]);
	int fd;
	int serial;
	char str1[20];
	int s;

	const char* str=argv[2];
	// Open the serial port
	fd = open(device, O_RDWR | O_NOCTTY | O_NDELAY);
	if (fd < 0) {
		perror("open");
		return 1;
	}
	strcpy(str1,"stty -F ");
	strcat(str1,device);
	strcat(str1," crtscts");
	int status=system(str1);

	printf("%s\n", device);
	const char o = '1';
	if (instance_number == 1) {
		instance1(fd);
	} else if (instance_number == 2) {
		instance2(fd);
	} else {
		fprintf(stderr, "Invalid instance number: %d\n", instance_number);
		exit(1);
	}

		// Assert DTR (set high)
/*		printf("here");
		if (ioctl(fd, TIOCMSET, TIOCM_DTR) != 0){// && ioctl(fd, TIOCMGET, &dtr_flag)==1) {
			perror("ioctl assert DTR");
			close(fd);
			return 1;
		} else {
			printf("DTR set high\n");
		}
		// Initialize instance1
	}else{
		// Clear DTR (set low)
		if (ioctl(fd, TIOCMBIC, TIOCM_DTR) != 0) {
			perror("ioctl clear DTR");
			close(fd);
			return 1;
		} else {
			printf("DTR set low\n");
		}
		instance1.value = 0;
	}
	setValueFromInstance(&instance2, &instance1);
	// Close the serial port
	int ret = ioctl(fd, TIOCMGET, &serial);
	if (serial & TIOCM_DTR)
		printf("%s: (after set) DTR is set\n", device);
	else
		printf("%s: (after set) DTR is NOT set\n", device);
	s=1;
	printf("Instance1 value: %d\n", instance1.value);
	printf("Instance2 value: %d\n", instance2.value);

	while (instance1.value==1){
	// Print values to verify
		printf("Instance1 value: %d\n", instance1.value);
		printf("Instance2 value: %d\n", instance2.value);

		usleep(1000);
	}
*/	close(fd);

	return 0;
}
