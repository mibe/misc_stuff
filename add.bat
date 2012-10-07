@echo off
SETLOCAL

SET AVRDUDE="avrdude"

IF NOT "%AVR32_HOME%" == "" SET AVRDUDE="%AVR32_HOME%\bin\avrdude.exe"

REM Simple batch script for calling avrdude with options for USBtinyISP
REM (C) 2012 Michael Bemmerl
REM License: WTFPL-2.0

IF "%1" == "" GOTO help

%AVRDUDE% -c usbtiny -P usb %*
GOTO exit

:help
echo You probably want to add the following options:
echo -p [partno]
echo -U flash:w:[file]
GOTO exit;

:exit
