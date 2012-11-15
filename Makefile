.PHONY: setup

setup: arcanist libphutil
	rm -f arc
	ln -s arcanist/bin/arc .

arcanist:
	git clone git://github.com/facebook/arcanist.git
	ln -s arcanist/bin/arc .

libphutil:
	git clone git://github.com/facebook/libphutil.git
	libphutil/scripts/build_xhpast.sh || true
	libphutil/scripts/build_xhpast.sh
