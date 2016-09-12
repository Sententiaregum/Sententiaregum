#!/usr/bin/env bash

if [ -e "./src/Frontend/config/recaptcha.js" ]; then
    echo -e "\e[31m You already have a recaptcha.js file! \e[0m"
else

    echo "export default ''; " > ./src/Frontend/config/recaptcha.js

    echo -e "\e[42m Created a recaptcha.js file in the config/ folder \e[0m";
    echo -e "\e[7m Now add your site-key there. OR: Create a new one: https://www.google.com/recaptcha/admin \e[0m";
fi
