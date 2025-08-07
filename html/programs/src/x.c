#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <termios.h>
#include <sys/ioctl.h>
#include <errno.h>

// Function to configure serial port
int configure_serial(int fd) {
    struct termios options;

    // Get current options for the port
    if (tcgetattr(fd, &options) < 0) {
        perror("tcgetattr");
        return -1;
    }

    // Enable RTS/CTS hardware flow control
    options.c_cflag |= (CLOCAL | CREAD);
    options.c_cflag |= CRTSCTS;

    // Set the new options for the port
    if (tcsetattr(fd, TCSANOW, &options) < 0) {
        perror("tcsetattr");
        return -1;
    }

    return 0;
}

// Function to set DTR line
int set_dtr(int fd, int level) {
    int control_bits;
int status;
    if (ioctl(fd, TIOCMGET, &control_bits) < 0) {
        perror("TIOCMGET");
        return -1;
    }
    printf("control_bits %i\n",control_bits & TIOCM_DTR);

printf("level %i\n",level);
    if (level == 0) {
        control_bits &= ~TIOCM_DTR; // Clear DTR
//        control_bits |= TIOCM_DSR; // Clear DTR
    } else {
        control_bits |= TIOCM_DTR; // Set DTR
//        control_bits |= TIOCM_DSR; // Clear DTR
    }
    printf("before\n");
 if (ioctl(fd, TIOCMGET, &status) == -1) {
        perror("ioctl TIOCMGET");
        close(fd);
        return 1;
    } 
    if (ioctl(fd, TIOCMGET, &control_bits) < 0) {
            perror("TIOCMGET");
            return -1;
        }
        printf("control_bits %i\n",control_bits & TIOCM_DTR);
    
        if (level == 0) {
            control_bits &= ~TIOCM_DTR; // Clear DTR
            control_bits |= TIOCM_DSR; // Clear DTR
        } else {
            control_bits |= TIOCM_DTR; // Set DTR
            control_bits |= TIOCM_DSR; // Clear DTR
        }
printf("before\n");

    printf("DTR: %s\n", (status & TIOCM_DTR) ? "set" : "cleared");
    printf("RTS: %s\n", (status & TIOCM_RTS) ? "set" : "cleared");
    printf("CTS: %s\n", (status & TIOCM_CTS) ? "set" : "cleared");
    printf("DSR: %s\n", (status & TIOCM_DSR) ? "set" : "cleared");
    printf("CAR/CD: %s\n", (status & TIOCM_CAR) ? "set" : "cleared");
    printf("RI: %s\n", (status & TIOCM_RI) ? "set" : "cleared");
    
    
    if (ioctl(fd, TIOCMSET, &control_bits) < 0) {
        perror("TIOCMSET");
        return -1;
    }
    printf("after\n");
     printf("DTR: %s\n", (status & TIOCM_DTR) ? "set" : "cleared");
    printf("RTS: %s\n", (status & TIOCM_RTS) ? "set" : "cleared");
    printf("CTS: %s\n", (status & TIOCM_CTS) ? "set" : "cleared");
    printf("DSR: %s\n", (status & TIOCM_DSR) ? "set" : "cleared");
    printf("CAR/CD: %s\n", (status & TIOCM_CAR) ? "set" : "cleared");
    printf("RI: %s\n", (status & TIOCM_RI) ? "set" : "cleared");
    
    return 0;
}

// Function to monitor CTS line using TIOCMIWAIT
void monitor_cts(int fd) {
    while (1) {
        int status;
        int status1;
        int status2;
        if (ioctl(fd, TIOCMGET, &status1) == -1) {
                perror("ioctl TIOCMGET");
                close(fd);
                return ;
            }
        status1 |= TIOCM_DTR;
       if (ioctl(fd, TIOCMGET, &status2) == -1) {
           perror("ioctl TIOCMGET");
           close(fd);
           return ;
       }
       status2 &= ~TIOCM_DTR;

        // Wait for CTS change
        if (ioctl(fd, TIOCMIWAIT, TIOCM_CTS) < 0) {
            perror("TIOCMIWAIT");
            break;
        }
printf("cts changed\n");
        if (ioctl(fd, TIOCMGET, &status) == -1) {
            perror("ioctl TIOCMGET");
            close(fd);
            return ;
        }
 
        if (status & TIOCM_CTS) {
           printf("CTS is ON\n");
           set_dtr(fd,1);
        } else {
            printf("CTS is OFF\n");
//            set_dtr(fd,0);
        }
    }
}

int main(int argc, char *argv[]) {
    int fd;
    int status;
    const char *device = "/dev/ttyUSB0"; // Replace with your serial port device

    // Open the serial port
    fd = open(device, O_RDWR | O_NOCTTY | O_NDELAY);
    if (fd == -1) {
        perror("open");
        return 1;
    }

    // Configure the serial port
    char *s = "stty -F /dev/ttyUSB0 9600 cs8 -parenb -cstopb crtscts";
    int t = system(s);
//    if (configure_serial(fd) < 0) {
//        close(fd);
//        return 1;
//    }
if (ioctl(fd, TIOCMGET, &status) == -1) {
        perror("ioctl TIOCMGET");
        close(fd);
        return 1;
    }
/*status |= TIOCM_CTS;
    if (ioctl(fd, TIOCMSET, &status) == -1) {
        perror("ioctl TIOCMSET");
        close(fd);
        return 1;
    }
*/
/*status &= ~TIOCM_DTR;
    if (ioctl(fd, TIOCMSET, &status) == -1) {
        perror("ioctl TIOCMSET");
        close(fd);
        return 1;
    }
*/
    // Set DTR to 0
    if (set_dtr(fd,0) < 0) {
        close(fd);
        return 1;
    }

    // Monitor CTS for changes
    monitor_cts(fd);

    // Close the serial port
    close(fd);

    return 0;
}
