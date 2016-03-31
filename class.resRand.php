<?php

/**
 * Description of resRand
 *
 * @author Loy Clements
 */
class resRandClass {

    //properties
    public $sourceList = [];
    public $workingList = [];
    public $rules = [];

    //methods
    public function import($userArray) {
	$this->sourceList = $userArray;
	$this->workingList = $userArray;
    }

    public function addRule($func, $rangeInt = 1, $isInclusive = true) {
	$newRule = [
	    "callback" => $func,
	    "range" => $rangeInt,
	    "inclusive" => $isInclusive
	];
	array_push($this->rules, $newRule);
    }

    public function randomise() {
	$this->workingList = [];
	$input = $this->sourceList;
	$output = &$this->workingList;
	$sourceNumbers = [];
	$inputLength = count($input);
	for ($i = 0; $i < $inputLength; $i++) {
	    array_push($sourceNumbers, $i);
	}
	unset($i);

	for ($i = 0; $i < $inputLength; $i++) {
	    $potentialEntry = rand(0, count($sourceNumbers) - 1);
	    $targetEntry = $input[$sourceNumbers[$potentialEntry]];
	    array_splice($sourceNumbers, $potentialEntry, 1);
	    array_push($output, $targetEntry);
	}
    }

    public function checkElementAgainstPosition($element, $position) {
	$rules = $this->rules;
	$rulesLength = count($rules);
	$input = $this->workingList;

	for ($rule = 0; $rule < $rulesLength; $rule++) {

	    $ruleRange = $rules[$rule]['range'];

	    $isRuleBroken = false;

	    for ($currentRange = 0; $currentRange < $ruleRange; $currentRange++) {
		$distance = $currentRange + 1;

		if ($distance <= $position && $rules[$rule]['callback']($element, $input[$position - $distance], $distance) || ( count($input) - $position) > $currentRange && $rules[$rule]['callback']($element, $input[$position + $currentRange], $distance)) {
		    //echo "found position conflict <br>";
		    $isRuleBroken = true;
		} else {
		    $isRuleBroken = false;
		    if ($rules[$rule]['inclusive']) {
			break;
		    }
		}
	    }

	    if ($isRuleBroken) {
		return true;
	    }
	}
	return false;
    }

    public function applyRules()
    {
	$input = &$this->workingList;
	$rules = $this->rules;
	$inputLength = count($this->workingList);
	$rulesLength = count($this->rules);

	for ($i = 0; $i < $inputLength; $i++)
	{
	    //echo "Checking index ".$i.". <br>";
	    $hasFault = false;

	    for ($rule = 0; $rule < $rulesLength; $rule++)
	    {
		$ruleRance = $rules[$rule]['range'];
		$ruleIsBroken = false;
		for ($currentRange = 0;$currentRange < $ruleRance;$currentRange++)
		{
		    $distance = $currentRange + 1;
		    if ($i >= $distance)
		    {
			$matches = $rules[$rule]['callback']($input[$i], $input[$i-$distance],$distance);
		    }

		    if ($matches)
		    {
			$ruleIsBroken = true;
			//echo "Found rule-breaking match at index ".$i.". <br>";
		    }
		    else
		    {
			if ($rules[$rule]['inclusive'])
			{
			    $ruleIsBroken = false;
			    break;
			}
		    }
		}
		if ($ruleIsBroken)
		{
		    $hasFault = true;
		}
	    }
	    if ($hasFault)
	    {
		$unshuffledNums = [];
		for ($j = 0; $j<$inputLength;$j++)
		{
		    array_push($unshuffledNums,$j);
		}
		$randomNums = [];
		while (count($unshuffledNums) > 0)
		{
		    array_push($randomNums, array_splice($unshuffledNums,rand(0,count($unshuffledNums)-1),1));
		}
	    }
	    while ($hasFault)
	    {
		if (count($randomNums) === 0)
		{
		    echo "Cannot sort list. Please try again, or apply less restrictive rules. <br>";
		    return false;
		}
		$randomElementNumber = array_shift($randomNums)[0];
		//echo "Checking ".$input[$i]." against element ".$randomElementNumber." <br>";

		if ($randomElementNumber !== $i and !($this->checkElementAgainstPosition($input[$i], $randomElementNumber)))
		{
		    $movingElement = array_splice($input, $i,1);
		    array_splice($input,$randomElementNumber,0,$movingElement[0]);
		    $i=0;
		    $hasFault = false;

		}

	    }
	   // print_r($this->workingList);
	}
    }

    public function go()
    {
	$this->randomise();
	$this->applyRules();
	return $this->workingList;
    }

    public function goPrint()
    {
	echo "Function goPrint() is empty. <br>";
    }

    public function export()
    {
	return $this->workingList;
    }

    public function printTable()
    {
	echo "Function printTable() is empty. <br>";
    }

    public function printCustom($callback,$header,$footer)
    {
	echo "Function printCustom() is empty. <br>";
    }

    public function printToConsole()
    {
	echo "<br>";
	$listLength = count($this->workingList);
	for ($i = 0; $i < $listLength;$i++)
	{
	    echo "".$this->workingList[$i]."<br>";
	}
    }
}

