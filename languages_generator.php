<?php

$text = <<<EOF
aa 	afar
ab 	abjasio (o abjasiano)
ae 	avéstico
af 	afrikáans
ak 	akano
am 	amhárico
an 	aragonés
ar 	árabe
as 	asamés
av 	avar (o ávaro)
ay 	aimara
az 	azerí
ba 	baskir
be 	bielorruso
bg 	búlgaro
bh 	bhoyapurí
bi 	bislama
bm 	bambara
bn 	bengalí
bo 	tibetano
br 	bretón
bs 	bosnio
ca 	catalán
ce 	checheno
ch 	chamorro
co 	corso
cr 	cree
cs 	checo
cu 	eslavo eclesiástico antiguo
cv 	chuvasio
cy 	galés
da 	danés
de 	alemán
dv 	maldivo (o dhivehi)
dz 	dzongkha
ee 	ewé
el 	griego (moderno)
en 	inglés
eo 	esperanto
es 	español (o castellano)
et 	estonio
eu 	euskera
fa 	persa
ff 	fula
fi 	finés (o finlandés)
fj 	fiyiano (o fiyi)
fo 	feroés
fr 	francés
fy 	frisón (o frisio)
ga 	irlandés (o gaélico)
gd 	gaélico escocés
gl 	gallego
gn 	guaraní
gu 	guyaratí (o gujaratí)
gv 	manés (gaélico manés o de Isla de Man)
ha 	hausa
he 	hebreo
hi 	hindi (o hindú)
ho 	hiri motu
hr 	croata
ht 	haitiano
hu 	húngaro
hy 	armenio
hz 	herero
ia 	interlingua
id 	indonesio
ie 	occidental
ig 	igbo
ii 	yi de Sichuán
ik 	iñupiaq
io 	ido
is 	islandés
it 	italiano
iu 	inuktitut (o inuit)
ja 	japonés
jv 	javanés
ka 	georgiano
kg 	kongo (o kikongo)
ki 	kikuyu
kj 	kuanyama
kk 	kazajo (o kazajio)
kl 	groenlandés (o kalaallisut)
km 	camboyano (o jemer)
kn 	canarés
ko 	coreano
kr 	kanuri
ks 	cachemiro (o cachemir)
ku 	kurdo
kv 	komi
kw 	córnico
ky 	kirguís
la 	latín
lb 	luxemburgués
lg 	luganda
li 	limburgués
ln 	lingala
lo 	lao
lt 	lituano
lu 	luba-katanga (o chiluba)
lv 	letón
mg 	malgache (o malagasy)
mh 	marshalés
mi 	maorí
mk 	macedonio
ml 	malayalam
mn 	mongol
mr 	maratí
ms 	malayo
mt 	maltés
my 	birmano
na 	nauruano
nb 	noruego bokmål
nd 	ndebele del norte
ne 	nepalí
ng 	ndonga
nl 	neerlandés (u holandés)
nn 	nynorsk
no 	noruego
nr 	ndebele del sur
nv 	navajo
ny 	chichewa
oc 	occitano
oj 	ojibwa
om 	oromo
or 	oriya
os 	osético (u osetio, u oseta)
pa 	panyabí (o penyabi)
pi 	pali
pl 	polaco
ps 	pastú (o pastún, o pashto)
pt 	portugués
qu 	quechua
rm 	romanche
rn 	kirundi
ro 	rumano
ru 	ruso
rw 	ruandés (o kiñaruanda)
sa 	sánscrito
sc 	sardo
sd 	sindhi
se 	sami septentrional
sg 	sango
si 	cingalés
sk 	eslovaco
sl 	esloveno
sm 	samoano
sn 	shona
so 	somalí
sq 	albanés
sr 	serbio
ss 	suazi (o swati, o siSwati)
st 	sesotho
su 	sundanés (o sondanés)
sv 	sueco
sw 	suajili
ta 	tamil
te 	télugu
tg 	tayiko
th 	tailandés
ti 	tigriña
tk 	turcomano
tl 	tagalo
tn 	setsuana
to 	tongano
tr 	turco
ts 	tsonga
tt 	tártaro
tw 	twi
ty 	tahitiano
ug 	uigur
uk 	ucraniano
ur 	urdu
uz 	uzbeko
ve 	venda
vi 	vietnamita
vo 	volapük
wa 	valón
wo 	wolof
xh 	xhosa
yi 	yídish (o yidis, o yiddish)
yo 	yoruba
za 	chuan (o chuang, o zhuang)
zh 	chino
zu 	zulú
EOF;

$lines = explode("\n", $text);

foreach ($lines as $line)
{
    $language = explode(" ", $line);
    echo "[ \"ISO_639_1\" => \"" . $language[0] . "\", \"name\" => \"" . trim($language[1]) . "\" ],\n";
}
