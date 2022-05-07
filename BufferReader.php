<?php

final class BufferReader{

    public static function concat(array $list, int $totalLength = null): BufferReader{
        if($totalLength === null){
            $totalLength = 0;
            foreach($list as $item){
                if(!$item instanceof BufferReader){
                    throw new \Exception('Invalid item in buffer list');
                }
                $totalLength += $item->getLength();
            }
        }
        $buffer = BufferReader::allocate($totalLength);
        $offset = 0;
        foreach($list as $item){
            if(!$item instanceof BufferReader){
                throw new \Exception('Invalid item in buffer list');
            }
            $buffer = $item->copy($buffer, $offset);
            $offset += $item->length;
        }
        return $buffer;
    }

    private function __construct(
        private string $buffer,
        private int $length
    ) {
    }

    public static function new(string $buffer): self
    {
        return new self($buffer, strlen($buffer));
    }

    public static function allocate(int $capacity): self
    {
        return new self(pack("x{$capacity}"), $capacity);
    }

    public function toString(): string
    {
        return $this->buffer;
    }

    public function copy(self &$target, int $targetStart = 0, int $sourceStart = 0, int $sourceEnd = 0)
    {
        if ($sourceEnd === 0) {
            $sourceEnd = $this->length;
        }

        $sourceEnd = min($sourceEnd, $this->length);
        $sourceStart = min($sourceStart, $sourceEnd);
        $targetStart = min($targetStart, $target->getLength());

        $target->write(substr($this->buffer, $sourceStart, $sourceEnd - $sourceStart), $targetStart);
    }

    //Implement Nodejs Buffer.slice( start, end ) with end optional
    public function slice(int $start, int $end = 0): self
    {
        if ($end === 0) {
            $end = $this->length;
        }

        $end = min($end, $this->length);
        $start = min($start, $end);

        return BufferReader::new(substr($this->buffer, $start, $end - $start));
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function write(string $data, int $offset): void
    {
        $this->buffer = substr_replace($this->buffer, $data, $offset, strlen($data));
        $this->length = strlen($this->buffer);
    }


    public function readUInt8(int $offset = 0): int
    {
        return unpack('C', $this->buffer, $offset)[1];
    }

    public function readUInt16LE(int $offset = 0): int
    {
        return unpack('v', $this->buffer, $offset)[1];
    }

    public function readInt32BE(int $offset = 0): int
    {
        return unpack('N', $this->buffer, $offset)[1];
    }

    public function readUInt32LE(int $offset = 0): int
    {
        return unpack('V', $this->buffer, $offset)[1];
    }

    public function writeUInt8(int $value, int $offset = 0): void
    {
        $this->write(pack('C', $value), $offset);
    }

    public function writeUInt16LE(int $value, int $offset = 0): void
    {
        $this->write(pack('v', $value), $offset);
    }

    public function writeUInt32BE(int $value, int $offset = 0): void
    {
        $this->write(pack('N', $value), $offset);
    }

    public function writeUInt32LE(int $value, int $offset = 0): void
    {
        $this->write(pack('V', $value), $offset);
    }
}
