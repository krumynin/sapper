<?php

namespace App\Service;

use App\Entity\Field;

/**
 * Class GameService
 */
class GameService
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param int $x
     * @param int $y
     *
     * @return array
     */
    private function getArray(int $x, int $y)
    {
        return [
            ['x' => $x + 1, 'y' => $y + 1],
            ['x' => $x + 1, 'y' => $y - 1],
            ['x' => $x - 1, 'y' => $y + 1],
            ['x' => $x - 1, 'y' => $y - 1],
            ['x' => $x + 1, 'y' => $y],
            ['x' => $x - 1, 'y' => $y],
            ['x' => $x, 'y' => $y + 1],
            ['x' => $x, 'y' => $y - 1],
        ];
    }

    /**
     * @param int   $x
     * @param int   $y
     * @param Field $field
     *
     * @return array
     */
    public function getResult(int $x, int $y, Field $field)
    {
        $this->data = $field->getData();
        $result = $this->goNext($x, $y);
        $field->setData($this->data);

        return $result;
    }

    /**
     * @param int   $x
     * @param int   $y
     * @param array $result
     *
     * @return array
     */
    private function goNext(int $x, int $y, array $result = []): array
    {
        $point = $this->data[$x][$y];
        $result[$x][$y] = $point;

        $array = $this->getArray($x, $y);

        $this->data[$x][$y]['clicked'] = true;
        if ($point['nearBomb'] === 0 && !$point['clicked']) {
            foreach ($array as $cell) {
                if (isset($this->data[$cell['x']][$cell['y']]) && !$this->data[$cell['x']][$cell['y']]['clicked']) {
                    $result = $this->goNext($cell['x'], $cell['y'], $result);
                }
            }
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function openCellAmount(array $data): int
    {
        $count = 0;
        for ($i = 0; $i < count($data); $i++) {
            for($j = 0; $j < count($data[$i]); $j++) {
                if ($data[$i][$j]['clicked']) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * @param int   $x
     * @param int   $y
     * @param Field $field
     *
     * @return array
     */
    public function openNear(int $x, int $y, Field $field)
    {
        $this->data = $field->getData();
        $result = [];
        $array = $this->getArray($x, $y);

        foreach ($array as $cell) {
            if (isset($this->data[$cell['x']][$cell['y']]) && !($this->data[$cell['x']][$cell['y']]['clicked']) && !($this->data[$cell['x']][$cell['y']]['marked'])) {
                $result = $this->goNext($cell['x'], $cell['y'], $result);
                $field->setData($this->data);
            }
        }

        $field->setData($this->data);

        return $result;
    }

    /**
     * @param int   $x
     * @param int   $y
     * @param Field $field
     *
     * @return int
     */
    public function nearMarked(int $x, int $y, Field $field)
    {
        $this->data = $field->getData();
        $array = $this->getArray($x, $y);
        $amountMarked = 0;

        foreach ($array as $cell) {
            if (isset($this->data[$cell['x']][$cell['y']]) && $this->data[$cell['x']][$cell['y']]['marked']) {
                $amountMarked++;
            }
        }

        return $amountMarked;
    }

    /**
     * @param int $vertical
     * @param int $horizontal
     * @param int $bombAmount
     *
     * @return array
     */
    public function fillData(int $vertical, int $horizontal, int $bombAmount): array
    {
        $data = [];

        $bomb = array_fill(0, $bombAmount, true);
        $bomb = array_pad($bomb, $vertical*$horizontal, false);
        shuffle($bomb);

        $count = 0;
        for ($y = 0; $y < $vertical; $y++) {
            for ($x = 0; $x < $horizontal; $x++) {
                $data[$x][$y] = [
                    'bomb' => $bomb[$count],
                    'nearBomb' => 0,
                    'clicked' => false,
                    'marked' => false,
                ];
                $count++;
            }
        }
        $data = $this->howManyBombs($vertical, $horizontal, $data);

        return $data;
    }

    private function howManyBombs(int $vertical, int $horizontal, $data)
    {
        $bigArray = [];
        $amount = [];

        for ($j = 0; $j < ($vertical + 2); $j++) {
            for ($i = 0; $i < ($horizontal + 2); $i++) {
                $bigArray[$i][$i] = 0;
                $amount[$i][$j] = 0;
            }
        }

        for ($j = 1; $j < ($vertical + 1); $j++) {
            for ($i = 1; $i < ($horizontal + 1); $i++) {
                if ($data[$i-1][$j-1]['bomb'] == true){
                    $amount[$i-1][$j-1]++;
                    $amount[$i-1][$j]++;
                    $amount[$i][$j-1]++;
                    $amount[$i+1][$j+1]++;
                    $amount[$i+1][$j]++;
                    $amount[$i][$j+1]++;
                    $amount[$i+1][$j-1]++;
                    $amount[$i-1][$j+1]++;
                };
            }
        }
        for ($j = 0; $j < ($vertical); $j++) {
            for ($i = 0; $i < ($horizontal); $i++) {
                $data[$i][$j]['nearBomb'] = $amount[$i+1][$j+1];
            }
        }

        return $data;
    }
}
