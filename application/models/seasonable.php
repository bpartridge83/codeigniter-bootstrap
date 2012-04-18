<?php
interface Seasonable {

    public function hasSeasons();
    public function hasSeason($year);
    public function getSeasons();
    public function getSeason($year);
    public function addSeason($season);

}