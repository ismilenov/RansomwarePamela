#!/bin/bash

crawl_directory() {
    local mode="$1"
    local current_dir="$2"
    local extensions="$3"
    local key="$4"
    local iv="$5"

    if [ ! -d "$current_dir" ]; then
        echo "The directory $current_dir does not exist!"
        return
    fi

    # Iterate through all files and directories in the current directory
    for file in "$current_dir"/*; do
        if [ -d "$file" ]; then
            # If it's a directory, call the function recursively
            crawl_directory "$mode" "$file" "$extensions" "$key" "$iv"
        elif [ -f "$file" ]; then
            # If it's a file, check against all extensions
            for ext in $extensions; do
                if [[ "$file" == *"$ext" ]]; then
                    echo "File with extension $ext: $file"
                    if [[ "$mode" == "decrypt" ]]; then
                        openssl enc -aes-128-cbc -d  -in "$file"  -out "$file.dec" -K "$key" -iv "$iv" 
                        cat "$file.dec" > "$file"
                        rm "$file.dec"
                    elif [[ "$mode" == "encrypt" ]]; then
                        openssl enc -aes-128-cbc  -in "$file" -out "$file.enc" -K "$key" -iv "$iv" 
                        cat "$file.enc" > "$file"
                        rm "$file.enc"
                    fi
                fi
            done
        fi
    done
}

# Starting arguments
mode="$1"
start_dir="$2"
extensions="$3"  # Space-separated list of extensions
key="$4"
iv="$5"


if [[ "$mode" == "encrypt" ]]; then 
    rm  ./target_directory
    cp -r ./db_samples target_directory
fi 
crawl_directory "$mode" "$start_dir" "$extensions" "$key" "$iv"
