<?php

interface IObserver {
    public function update($message);
}

// Subject interface
interface ISubject {
    public function registerObserver(IObserver $observer);
    public function removeObserver(IObserver $observer);
    public function notifyObservers($message);
}


?>
