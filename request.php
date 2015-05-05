<?php

class People {

    private $list;

    private $orderBy;

    /**
     * The class constructor
     */
    public function __construct($orderBy) {
        $this->orderBy = $orderBy;
        $this->list = new SplDoublyLinkedList();
    }

    /**
     * The class destructor
     */
    public function __destructor() {
        unset($this);
    }

    public function add(Person $person) {
        if($this->orderBy != 'none') {
            $foundAt = $this->find($person);
            if($foundAt < 0) { // item not in list
                $pos = -$foundAt -1; //position where item should be inserted
                $this->list->add($pos, $person);
            } else {
                if($this->orderBy != 'name') { // we want to keep duplicates for other values (height, gender, birthdays could be duplicates)
                    $this->list->add($foundAt, $person);
                }
            }
        } else {
            $this->list->push($person); // add unsorted element to the list
        }
    }

    public function getList() {
        return $this->list;
    }

    /**
     * Performs binary search on People. If Person is not in the collection, it returns
     * a signal that gives the location where the Person should be inserted into the list.
     * @param Person $person the person to search for
     * @return int returns the position of the item, if item is not in the list,
     * returns signal that gives location where it should be inserted
     */
    private function find(Person &$person) {
        $low = 0;
        $high = $this->list->count() - 1;

        while($low <= $high) {

            $mid = ($low + $high) >> 1; //shift the bits of ($low+$hight) result to the right (each step means "divide by two")
            $tmp = $this->list->offsetGet($mid);

            $result = $person->compareToIgnoreCase($tmp, $this->orderBy);

            if($result == 0) //Person has been found, return its location
                return $mid;
            if($result < 0) //Person comes before middle element, search the top of the list
                $high = $mid - 1;
            else           //Person comes after the middle element, search the bottom of the list
                $low = $mid + 1;
        }
        //Person is not in the list, return a signal that gives the location where
        //the Person should be inserted into the list.
        return -$low - 1;
    }
}

class Person {
    private $name;
    private $height;
    private $gender;
    private $birthdate;

    /**
     * The class constructor
     */
    public function __construct($name, $height, $gender, DateTime $birthdate) {
        $this->name = $name;
        $this->height = $height;
        $this->gender = $gender;
        $this->birthdate = $birthdate;
    }

    /**
     * The class destructor
     */
    public function __destructor() {
        unset($this);
    }

    /**
     * Get the name
     *
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the name
     *
     * @param string name
     *
     * @return self
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of Height
     *
     * @return int
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Set the value of Height
     *
     * @param int height
     *
     * @return self
     */
    public function setHeight($height) {
        $this->height = $height;
        return $this;
    }

    /**
     * Get the value of Gender
     *
     * @return string
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * Set the value of Gender
     *
     * @param string gender
     *
     * @return self
     */
    public function setGender($gender) {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get the value of Birthday
     *
     * @return DateTime
     */
    public function getBirthday() {
        return $this->birthdate;
    }

    /**
     * Set the value of Birthday
     *
     * @param DateTime birthdate
     *
     * @return self
     */
    public function setBirthday(DateTime $birthdate) {
        $this->birthdate = $birthdate;
        return $this;
    }

    /**
     * Binary safe case-insensitive comparison of this Person with other Person.
     * @param Person oPerson
     * @return int Returns < 0 if this Person is less than oPerson;
     *                     > 0 if this Person is greater than oPerson, and
     *                       0 if they are equal.
     * @throws UnexpectedValueException
     */
    public function compareToIgnoreCase(Person $oPerson, $compareBy) {
        switch($compareBy) {
            case 'name':
                $tokens = explode(' ', $this->getName());
                $lastName = end($tokens);
                $tokens = explode(' ', $oPerson->getName());
                $oLastName = end($tokens);
                return strcasecmp($lastName, $oLastName);
                break;
            case 'height':
                if($this->getHeight() < $oPerson->getHeight()) return -1;
                elseif($this->getHeight() > $oPerson->getHeight()) return 1;
                else return 0;
                break;
            case 'gender':
                return strcasecmp($this->getGender(), $oPerson->getGender());
                break;
            case 'birthdate':
                if($this->getBirthday() < $oPerson->getBirthday()) return -1;
                elseif($this->getBirthday() > $oPerson->getBirthday()) return 1;
                else return 0;
                break;
            default:
                throw Exception('Unexpected sort value encounted.');
                break;
        }
    }

}

// Parse CSV
// Create a person for each record
// associate it to the higher class People
// return data back to index.php
header('content-type: application/json; charset=utf-8');

$orderBy = !empty($_GET['orderBy'])? $_GET['orderBy'] : 'none';
try {
    $people = new People($orderBy);
    $file   = new SplFileObject('people.csv');

    $file->setFlags(SplFileObject::SKIP_EMPTY);

    if ($file->isReadable()) {
        $col_names = $file->fgetcsv();

        while (!$file->eof()) {
            $row = $file->fgetcsv();
            if (!empty($row)) {
                $people->add(new Person($row[0], $row[1], $row[2], new DateTime($row[3])));
            }
        }
        $result = [];
        $data = $people->getList();
        for($data->rewind(); $data->valid(); $data->next()) {
            $person = $data->current();
            $result[] = [
                'name'      => $person->getName(),
                'height'    =>$person->getHeight(),
                'gender'    =>$person->getGender(),
                'birthdate' => $person->getBirthday()->format('Y-m-d')
                ];
        }
        echo json_encode($result);

    } else {
        echo json_encode(['error' => true, 'message' => 'Unable to read people.csv']);
    }
} catch(Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
