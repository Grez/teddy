.PHONY: clean

clean:
	rm prace.{aux,bbl,bcf,blg,idx,ind,lol,log,out,pdf,run.xml}

prace.pdf: prace.tex
	pdflatex prace.tex
	texindy -I omega -L czech prace.idx
	biber prace.bcf
	pdflatex prace.tex
