#!/usr/bin/env bash

./bin/console app:songs:import:google_play_music -vv
./bin/console app:songs:import:last_fm -vv
./bin/console app:songs:import:media_monkey -vv

./bin/console app:lib:aggregate-playbacks -vv

./bin/console app:songs:update -vv

./bin/console app:lib:flatten_songs -vv
./bin/console app:lib:create-duplicate-proposals -vv

./bin/console app:playlist:build:duplicates -vv
./bin/console app:playlist:build:flashing-tunes -vv
./bin/console app:playlist:build:lost-tunes -vv
./bin/console app:playlist:build:obsessions -vv
./bin/console app:playlist:export:media-monkey -vv
