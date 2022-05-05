<?php

namespace TeyvatPS\utils;

final class BufferReader{

    public static function new(string $buffer): self{
        return new self($buffer, strlen($buffer));
    }

    public static function allocate(int $capacity): self{
        return new self(pack("x{$capacity}"), $capacity);
    }

    private function __construct(
        private string $buffer,
        private int $length
    ){
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getRawBuffer(): string
    {
        return $this->buffer;
    }

    public function readInt32BE(int $offset = 0): int{
        return unpack('N', $this->buffer, $offset)[1];
    }

    public function writeUInt32BE(int $value, int $offset = 0): void{
        $overwrite = pack('N', $value);
        for ($i = 0; $i < strlen($overwrite); $i++) {
            $this->buffer[$offset++] = $overwrite[$i];
        }
    }
}