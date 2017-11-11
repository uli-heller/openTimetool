README.uli.md
=============

Hier beschreibe ich meine Tätigkeiten
am "opemTimetool". Im Moment bin ich in der
Evaluierungsphase. Kimai ist eine Alternative.

Lizenz
------

GPL - siehe [LICENSE](LICENSE).
Damit spricht nichts dagegen, den Quellcode in
[Github](https://github.com) zu veröffentlichen und bearbeiten.

Quellcode
---------

TBD

Git
---

### Kopieren von SourceForge

```
git svn clone --prefix=svnclone/ --stdlayout \
  https://svn.code.sf.net/p/opentimetool/code/openTimetool
cd openTimetool
git branch -m uli-master trunk
```

### Importieren in  Github

Über die Github-UI. Als Repository-URL habe ich
dies angegeben: https://svn.code.sf.net/p/opentimetool/code/openTimetool.
Damit werden auch gleich die Releases/Tags importiert.

### Abgleich

Ich möchte das Original-SVN-Repo weiter im Auge behalten.
Deshalb "verschmelze" ich beide Dinge:

```
git remote add origin git@github.com:uli-heller/openTimetool.git
git fetch origin
git checkout -b master origin/master
git diff master trunk # Kein Unterschied
LANG=C git merge trunk --allow-unrelated-histories
git push
#
git checkout uli-master
git rebase master
git checkout master
git rebase uli-master
git branch -d uli-master
git push
```
