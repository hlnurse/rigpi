#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/ioctl.h>
#include <linux/serial.h>

void set_dtr(int fd, int level) {
    int status;

    ioctl(fd, TIOCMGET, &status);
    if (level) {
        status |= TIOCM_DTR;  // Set DTR
    } else {
        status &= ~TIOCM_DTR; // Clear DTR
    }
    ioctl(fd, TIOCMSET, &status);
}

void set_rts(int fd, int level) {
    int status;

    ioctl(fd, TIOCMGET, &status);
    if (level==1) {
        status |= TIOCM_RTS;  // Set RTS
    } else {
        status &= ~TIOCM_RTS; // Clear RTS
    }
    ioctl(fd, TIOCMSET, &status);
}

int main(int argc, char *argv[]) {
//int main() {
//printf("ip to open: %i",argc);
    int fd = open(argv[1], O_RDWR | O_NOCTTY);
    if (fd == -1) {
        perror("open");
        exit(EXIT_FAILURE);
    }
int status;
    // Set DTR
 //   set_rts(fd, 0);
 //   sleep(2); // Keep DTR high for 2 seconds

    // Clear DTR
    set_rts(fd, 0);
//sleep(2);
 //   set_rts(fd,0);
//sleep(2);
int lines= TIOCM_CTS;
while(1){
int ret = ioctl(fd, TIOCMIWAIT, lines);

//        if (ioctl(fd, TIOCMIWAIT, TIOCM_CTS) < 0) {
//            perror("TIOCMIWAIT");
//            break;
//        }
    ioctl(fd, TIOCMGET, &status);
    printf("status %d\n",status);
        if (status & TIOCM_CTS) {
               printf("0\n");
               set_rts(fd,1);
               fflush(stdout);
            } else {
                printf("1\n");
                set_rts(fd,0);
                fflush(stdout);
            }
}
    close(fd);
    return 0;
}
