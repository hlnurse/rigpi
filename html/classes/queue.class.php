class Ds\Queue implements Ds\Collection, ArrayAccess {
/* Constants */
const int MIN_CAPACITY = 8;
/* Methods */
public allocate(int $capacity): void
public capacity(): int
public clear(): void
public copy(): Ds\Queue
public isEmpty(): bool
public peek(): mixed
public pop(): mixed
public push(mixed ...$values): void
public toArray(): array
}
