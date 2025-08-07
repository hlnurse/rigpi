#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <fcntl.h>
#include <sys/mman.h>
#include <sys/stat.h>
#include <sys/types.h>

#define SHARED_MEMORY_NAME "/shared_mem_example"
#define SHARED_MEMORY_SIZE sizeof(int)

void instance1() {
	// Create shared memory
	int shm_fd = shm_open(SHARED_MEMORY_NAME, O_CREAT | O_RDWR, 0666);
	ftruncate(shm_fd, SHARED_MEMORY_SIZE);
	int* shared_var = (int*)mmap(0, SHARED_MEMORY_SIZE, PROT_READ | PROT_WRITE, MAP_SHARED, shm_fd, 0);

	// Initialize shared variable
	*shared_var = 0;

	while (1) {
		printf("Instance 1: Shared variable = %d\n", *shared_var);
		sleep(1);
	}

	// Clean up
	munmap(shared_var, SHARED_MEMORY_SIZE);
	close(shm_fd);
	shm_unlink(SHARED_MEMORY_NAME);
}

void instance2() {
	// Open existing shared memory
	int shm_fd = shm_open(SHARED_MEMORY_NAME, O_RDWR, 0666);
	int* shared_var = (int*)mmap(0, SHARED_MEMORY_SIZE, PROT_READ | PROT_WRITE, MAP_SHARED, shm_fd, 0);

	// Modify shared variable
	for (int i = 1; i <= 5; ++i) {
		*shared_var = i * 10;
		printf("Instance 2: Set shared variable to %d\n", *shared_var);
		sleep(2);
	}

	// Clean up
	munmap(shared_var, SHARED_MEMORY_SIZE);
	close(shm_fd);
}

int main(int argc, char* argv[]) {
	if (argc != 2) {
		fprintf(stderr, "Usage: %s <instance_number>\n", argv[0]);
		exit(1);
	}

	int instance_number = atoi(argv[1]);
	if (instance_number == 1) {
		instance1();
	} else if (instance_number == 2) {
		instance2();
	} else {
		fprintf(stderr, "Invalid instance number: %d\n", instance_number);
		exit(1);
	}

	return 0;
}
