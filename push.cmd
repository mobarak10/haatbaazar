@echo off
set /P msg="Enter the Commit Message: " Rem Set the commit message
git add . &
git commit -m "%msg%" &
git push
Rem @pause