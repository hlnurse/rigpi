#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <string.h>
#include <sys/ioctl.h>
#include <termios.h>
#include <time.h>
#include <ctype.h> // Include this header for toupper

#define DOT_DURATION 100  // Duration of a dot in milliseconds
#define DASH_DURATION 300  // Duration of a dash in milliseconds
#define SYMBOL_SPACE 100  // Space between symbols within a character in milliseconds
#define LETTER_SPACE 300  // Space between letters in milliseconds
#define WORD_SPACE 700  // Space between words in milliseconds

// Function to configure the serial port
int configure_serial_port(int fd) {
	struct termios options;

	// Get the current options for the port
	if (tcgetattr(fd, &options) == -1) {
		perror("tcgetattr");
		return -1;
	}

	// Set the baud rates to 9600
	cfsetispeed(&options, B9600);
	cfsetospeed(&options, B9600);

	// Enable the receiver and set local mode
	options.c_cflag |= (CLOCAL | CREAD);

	// Set the character size to 8 bits
	options.c_cflag &= ~CSIZE;
	options.c_cflag |= CS8;

	// Disable parity
	options.c_cflag &= ~PARENB;

	// Set stop bit to 1
	options.c_cflag &= ~CSTOPB;

	// Enable hardware flow control
	options.c_cflag |= CRTSCTS;

	// Set the new options for the port
	if (tcsetattr(fd, TCSANOW, &options) == -1) {
		perror("tcsetattr");
		return -1;
	}

	return 0;
}

// Function to sleep for a specified number of milliseconds
void msleep(int milliseconds) {
	struct timespec ts;
	ts.tv_sec = milliseconds / 1000;
	ts.tv_nsec = (milliseconds % 1000) * 1000000;
	nanosleep(&ts, NULL);
}

// Function to send a Morse code symbol using CTS
void send_symbol(int fd, char symbol) {
	int status;

	if (symbol == '.') {
		ioctl(fd, TIOCMGET, &status);
		status |= TIOCM_RTS;
		ioctl(fd, TIOCMSET, &status);
		msleep(DOT_DURATION);
		status &= ~TIOCM_RTS;
		ioctl(fd, TIOCMSET, &status);
	} else if (symbol == '-') {
		ioctl(fd, TIOCMGET, &status);
		status |= TIOCM_RTS;
		ioctl(fd, TIOCMSET, &status);
		msleep(DASH_DURATION);
		status &= ~TIOCM_RTS;
		ioctl(fd, TIOCMSET, &status);
	}
	msleep(SYMBOL_SPACE);
}

// Function to send Morse code for a single character
void send_morse_character(int fd, char c) {
	const char* morse_code[] = {
		".-", "-...", "-.-.", "-..", ".", "..-.", "--.", "....", "..", ".---",
		"-.-", ".-..", "--", "-.", "---", ".--.", "--.-", ".-.", "...", "-",
		"..-", "...-", ".--", "-..-", "-.--", "--.."
	};

	if (c >= 'A' && c <= 'Z') {
		const char* code = morse_code[c - 'A'];
		for (int i = 0; code[i] != '\0'; i++) {
			send_symbol(fd, code[i]);
		}
	}
	msleep(LETTER_SPACE - SYMBOL_SPACE);  // Adjust space between letters
}

// Function to send Morse code message
void send_morse_message(int fd, const char* message) {
	for (int i = 0; message[i] != '\0'; i++) {
		if (message[i] == ' ') {
			msleep(WORD_SPACE - LETTER_SPACE);  // Adjust space between words
		} else {
			send_morse_character(fd, toupper(message[i]));
		}
	}
}

int main() {
	// Open the serial port
	int fd = open("/dev/ttyUSB0", O_RDWR | O_NOCTTY);
	if (fd == -1) {
		perror("open");
		return -1;
	}

	// Configure the serial port
	if (configure_serial_port(fd) == -1) {
		close(fd);
		return -1;
	}

	// Morse code message to send
	const char* message = "HELLO WORLD";

	// Send the Morse code message
	send_morse_message(fd, message);

	// Close the serial port
	close(fd);
	return 0;
}