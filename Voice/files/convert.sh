for a in *.wav; do  sox "$a" -t raw -r 8000 -d -2 -c 1 ${s/.wav/.sln/} resample -ql; done