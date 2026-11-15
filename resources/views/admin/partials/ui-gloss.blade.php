<style>
    /* kartu statistik: sudut organik + kilau */
    .ab-bento .ab-tile { border-radius:50px 16px 50px 16px; box-shadow:0 20px 44px rgba(124,92,224,.16), inset 0 2px 0 rgba(255,255,255,.95), inset 0 -16px 30px rgba(190,170,255,.14); }
    .ab-bento .ab-tile:nth-child(even) { border-radius:16px 50px 16px 50px; }
    .ab-bento .ab-tile:not(.hero) { background-image:linear-gradient(115deg, transparent 26%, rgba(255,255,255,.7) 46%, transparent 64%); }
    .ab-bento .ab-tile:not(.hero)::after { content:''; position:absolute; right:22px; top:20px; width:16px; height:16px; clip-path:var(--star); background:linear-gradient(135deg,#fff,#d9c8ff); animation:abTwinkle 4s ease-in-out infinite; }
    .ab-bento .ab-tile.hero { box-shadow:0 26px 54px rgba(124,92,224,.45), inset 0 2px 0 rgba(255,255,255,.5), inset 0 -18px 30px rgba(255,255,255,.12); }

    /* bar cari: kaca berkilau */
    .ab-bar { position:relative; background:linear-gradient(135deg,rgba(255,255,255,.94),rgba(247,240,255,.8)); box-shadow:inset 0 2px 0 #fff, 0 20px 46px rgba(124,92,224,.17), 0 0 0 1px rgba(200,175,255,.45); }
    .ab-seg input:checked + span { background:linear-gradient(120deg,#7c5ce0,#f06fb0); color:#fff; box-shadow:0 8px 18px rgba(124,92,224,.4), inset 0 1px 0 rgba(255,255,255,.4); }

    /* kartu buku: bingkai kaca asimetris */
    .ab-shelf { gap:36px 26px; }
    .ab .ab-bk { position:relative; padding:10px 10px 18px; border-radius:38px 14px 38px 14px; background:linear-gradient(150deg,rgba(255,255,255,.88),rgba(255,255,255,.42)); border:1px solid rgba(255,255,255,.95); box-shadow:0 22px 46px rgba(124,92,224,.15), inset 0 2px 0 #fff; backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); animation:abRise .5s ease backwards; transition:box-shadow .3s ease; }
    .ab .ab-bk:nth-child(even) { border-radius:14px 38px 14px 38px; }
    .ab .ab-bk:hover { box-shadow:0 32px 64px rgba(124,92,224,.3), 0 0 46px rgba(240,111,176,.28), inset 0 2px 0 #fff; }
    .ab .ab-poster { border-radius:28px 8px 28px 8px; box-shadow:0 16px 30px rgba(60,40,140,.32); }
    .ab .ab-bk:nth-child(even) .ab-poster { border-radius:8px 28px 8px 28px; }
    .ab .ab-bk:hover .ab-poster { transform:translateY(-4px) scale(1.03) rotate(-1deg); box-shadow:0 30px 52px rgba(60,40,140,.4); }
    .ab .ab-info { padding:0 8px; }
    .ab-pill { box-shadow:0 6px 14px rgba(60,40,140,.2), inset 0 1px 0 #fff; }
</style>