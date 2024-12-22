<?PHP

  $spacer_vertical = 30;        /* space between columns */
  $spacer_horizontal = 20;      /* space between rows */
  $margin_top = 0;              /* default top distance of columns block */

?>

.wb_colgroup {
    margin-top: <?PHP echo $margin_top; ?>px;
    width: 100%;
}

.wb_colrow {
    width: calc(100% + <?PHP echo $spacer_vertical; ?>px);
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-flex: 0 1 auto;
    -ms-flex: 0 1 auto;
    flex: 0 1 auto;
    -webkit-flex-direction: row;
    -ms-flex-direction: row;
    flex-direction: row;
    -webkit-flex-wrap: wrap;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin: -<?PHP echo $spacer_horizontal/2; ?>px -<?PHP echo $spacer_vertical/2; ?>px;
}

.wb_colalign_top, .wb_colalign_middle, .wb_colalign_bottom, .wb_colalign_full {
    -webkit-flex: 0 0 auto;
    -ms-flex: 0 0 auto;
    -webkit-box-flex: 0;
    flex: 0 0 auto;
    width: 100%;
}

.wb_colalign_top {
    -webkit-align-self: flex-start;
    -ms-align-self: flex-start;
    align-self: flex-start;
}

.wb_colalign_middle {
    -webkit-align-self: center;
    -ms-align-self: center;
    align-self: center;
}

.wb_colalign_bottom {
    -webkit-align-self: flex-end;
    -ms-align-self: flex-end;
    align-self: flex-end;
}

.wb_colalign_full {
    -webkit-align-self: stretch;
    -ms-align-self: stretch;
    align-self: stretch;
    position: relative;
    overflow: hidden;
}

@media screen and (max-width: 650px) {
    .wb_colsmall_1, .wb_colsmall_2, .wb_colsmall_3, .wb_colsmall_4, .wb_colsmall_5, .wb_colsmall_6,
    .wb_colsmall_7, .wb_colsmall_8, .wb_colsmall_9, .wb_colsmall_10, .wb_colsmall_11, .wb_colsmall_12 {
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-flex: 0 1 auto;
        -ms-flex: 0 1 auto;
        flex: 0 1 auto;
        -webkit-flex-direction: row;
        -ms-flex-direction: row;
        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        flex-direction: row;
        -webkit-flex-wrap: wrap;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        box-sizing: border-box;
        padding: <?PHP echo $spacer_horizontal/2; ?>px <?PHP echo $spacer_vertical/2; ?>px;
    }
    
    .wb_colsmall_hide {
        display: none;
    }
    
    .wb_colsmall_1 {
        -webkit-flex-basis: 8.333%;
        -ms-flex-preferred-size: 8.333%;
        flex-basis: 8.333%;
        max-width: 8.333%;
    }
    
    .wb_colsmall_2 {
        -webkit-flex-basis: 16.667%;
        -ms-flex-preferred-size: 16.667%;
        flex-basis: 16.667%;
        max-width: 16.667%;
    }
    
    .wb_colsmall_3 {
        -webkit-flex-basis: 25%;
        -ms-flex-preferred-size: 25%;
        flex-basis: 25%;
        max-width: 25%;
    }
    
    .wb_colsmall_4 {
        -webkit-flex-basis: 33.333%;
        -ms-flex-preferred-size: 33.333%;
        flex-basis: 33.333%;
        max-width: 33.333%;
    }
    
    .wb_colsmall_5 {
        -webkit-flex-basis: 41.667%;
        -ms-flex-preferred-size: 41.667%;
        flex-basis: 41.667%;
        max-width: 41.667%;
    }
    
    .wb_colsmall_6 {
        -webkit-flex-basis: 50%;
        -ms-flex-preferred-size: 50%;
        flex-basis: 50%;
        max-width: 50%;
    }
    
    .wb_colsmall_7 {
        -webkit-flex-basis: 58.333%;
        -ms-flex-preferred-size: 58.333%;
        flex-basis: 58.333%;
        max-width: 58.333%;
    }
    
    .wb_colsmall_8 {
        -webkit-flex-basis: 66.667%;
        -ms-flex-preferred-size: 66.667%;
        flex-basis: 66.667%;
        max-width: 66.667%;
    }
    
    .wb_colsmall_9 {
        -webkit-flex-basis: 75%;
        -ms-flex-preferred-size: 75%;
        flex-basis: 75%;
        max-width: 75%;
    }
    
    .wb_colsmall_10 {
        -webkit-flex-basis: 83.333%;
        -ms-flex-preferred-size: 83.333%;
        flex-basis: 83.333%;
        max-width: 83.333%;
    }
    
    .wb_colsmall_11 {
        -webkit-flex-basis: 91.667%;
        -ms-flex-preferred-size: 91.667%;
        flex-basis: 91.667%;
        max-width: 91.667%;
    }
    
    .wb_colsmall_12 {
        -webkit-flex-basis: 100%;
        -ms-flex-preferred-size: 100%;
        flex-basis: 100%;
        max-width: 100%;
    }
    
    .wb_colorder_s1 {
        -webkit-order: 1;
        -ms-flex-order: 1;
        order: 1;
    }
    
    .wb_colorder_s2 {
        -webkit-order: 2;
        -ms-flex-order: 2;
        order: 2;
    }
    
    .wb_colorder_s3 {
        -webkit-order: 3;
        -ms-flex-order: 3;
        order: 3;
    }
    
    .wb_colorder_s4 {
        -webkit-order: 4;
        -ms-flex-order: 4;
        order: 4;
    }
    
    .wb_colorder_s5 {
        -webkit-order: 5;
        -ms-flex-order: 5;
        order: 5;
    }
    
    .wb_colorder_s6 {
        -webkit-order: 6;
        -ms-flex-order: 6;
        order: 6;
    }
    
    .wb_colorder_s7 {
        -webkit-order: 7;
        -ms-flex-order: 7;
        order: 7;
    }
    
    .wb_colorder_s8 {
        -webkit-order: 8;
        -ms-flex-order: 8;
        order: 8;
    }
    
    .wb_colorder_s9 {
        -webkit-order: 9;
        -ms-flex-order: 9;
        order: 9;
    }
    
    .wb_colorder_s10 {
        -webkit-order: 10;
        -ms-flex-order: 10;
        order: 10;
    }
    
    .wb_colorder_s11 {
        -webkit-order: 11;
        -ms-flex-order: 11;
        order: 11;
    }
    
    .wb_colorder_s12 {
        -webkit-order: 12;
        -ms-flex-order: 12;
        order: 12;
    }
}

@media screen and (min-width: 651px) and (max-width: 900px) {
    .wb_colmedium_1, .wb_colmedium_2, .wb_colmedium_3, .wb_colmedium_4, .wb_colmedium_5, .wb_colmedium_6,
    .wb_colmedium_7, .wb_colmedium_8, .wb_colmedium_9, .wb_colmedium_10, .wb_colmedium_11, .wb_colmedium_12 {
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-flex: 0 1 auto;
        -ms-flex: 0 1 auto;
        flex: 0 1 auto;
        -webkit-flex-direction: row;
        -ms-flex-direction: row;
        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        flex-direction: row;
        -webkit-flex-wrap: wrap;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        box-sizing: border-box;
        padding: <?PHP echo $spacer_horizontal/2; ?>px <?PHP echo $spacer_vertical/2; ?>px;
    }

    .wb_colmedium_hide {
        display: none;
    }
    
    .wb_colmedium_1 {
        -webkit-flex-basis: 8.333%;
        -ms-flex-preferred-size: 8.333%;
        flex-basis: 8.333%;
        max-width: 8.333%;
    }
    
    .wb_colmedium_2 {
        -webkit-flex-basis: 16.667%;
        -ms-flex-preferred-size: 16.667%;
        flex-basis: 16.667%;
        max-width: 16.667%;
    }
    
    .wb_colmedium_3 {
        -webkit-flex-basis: 25%;
        -ms-flex-preferred-size: 25%;
        flex-basis: 25%;
        max-width: 25%;
    }
    
    .wb_colmedium_4 {
        -webkit-flex-basis: 33.333%;
        -ms-flex-preferred-size: 33.333%;
        flex-basis: 33.333%;
        max-width: 33.333%;
    }
    
    .wb_colmedium_5 {
        -webkit-flex-basis: 41.667%;
        -ms-flex-preferred-size: 41.667%;
        flex-basis: 41.667%;
        max-width: 41.667%;
    }
    
    .wb_colmedium_6 {
        -webkit-flex-basis: 50%;
        -ms-flex-preferred-size: 50%;
        flex-basis: 50%;
        max-width: 50%;
    }
    
    .wb_colmedium_7 {
        -webkit-flex-basis: 58.333%;
        -ms-flex-preferred-size: 58.333%;
        flex-basis: 58.333%;
        max-width: 58.333%;
    }
    
    .wb_colmedium_8 {
        -webkit-flex-basis: 66.667%;
        -ms-flex-preferred-size: 66.667%;
        flex-basis: 66.667%;
        max-width: 66.667%;
    }
    
    .wb_colmedium_9 {
        -webkit-flex-basis: 75%;
        -ms-flex-preferred-size: 75%;
        flex-basis: 75%;
        max-width: 75%;
    }
    
    .wb_colmedium_10 {
        -webkit-flex-basis: 83.333%;
        -ms-flex-preferred-size: 83.333%;
        flex-basis: 83.333%;
        max-width: 83.333%;
    }
    
    .wb_colmedium_11 {
        -webkit-flex-basis: 91.667%;
        -ms-flex-preferred-size: 91.667%;
        flex-basis: 91.667%;
        max-width: 91.667%;
    }
    
    .wb_colmedium_12 {
        -webkit-flex-basis: 100%;
        -ms-flex-preferred-size: 100%;
        flex-basis: 100%;
        max-width: 100%;
    }
    
    .wb_colorder_m1 {
        -webkit-order: 1;
        -ms-flex-order: 1;
        order: 1;
    }
    
    .wb_colorder_m2 {
        -webkit-order: 2;
        -ms-flex-order: 2;
        order: 2;
    }
    
    .wb_colorder_m3 {
        -webkit-order: 3;
        -ms-flex-order: 3;
        order: 3;
    }
    
    .wb_colorder_m4 {
        -webkit-order: 4;
        -ms-flex-order: 4;
        order: 4;
    }
    
    .wb_colorder_m5 {
        -webkit-order: 5;
        -ms-flex-order: 5;
        order: 5;
    }
    
    .wb_colorder_m6 {
        -webkit-order: 6;
        -ms-flex-order: 6;
        order: 6;
    }
    
    .wb_colorder_m7 {
        -webkit-order: 7;
        -ms-flex-order: 7;
        order: 7;
    }
    
    .wb_colorder_m8 {
        -webkit-order: 8;
        -ms-flex-order: 8;
        order: 8;
    }
    
    .wb_colorder_m9 {
        -webkit-order: 9;
        -ms-flex-order: 9;
        order: 9;
    }
    
    .wb_colorder_m10 {
        -webkit-order: 10;
        -ms-flex-order: 10;
        order: 10;
    }
    
    .wb_colorder_m11 {
        -webkit-order: 11;
        -ms-flex-order: 11;
        order: 11;
    }
    
    .wb_colorder_m12 {
        -webkit-order: 12;
        -ms-flex-order: 12;
        order: 12;
    }
}

@media screen and (min-width: 901px) {
    .wb_collarge_1, .wb_collarge_2, .wb_collarge_3, .wb_collarge_4, .wb_collarge_5, .wb_collarge_6,
    .wb_collarge_7, .wb_collarge_8, .wb_collarge_9, .wb_collarge_10, .wb_collarge_11, .wb_collarge_12 {
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-flex: 0 1 auto;
        -ms-flex: 0 1 auto;
        flex: 0 1 auto;
        -webkit-flex-direction: row;
        -ms-flex-direction: row;
        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        flex-direction: row;
        -webkit-flex-wrap: wrap;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        box-sizing: border-box;
        padding: <?PHP echo $spacer_horizontal/2; ?>px <?PHP echo $spacer_vertical/2; ?>px;
    }

    .wb_collarge_hide {
        display: none;
    }
    
    .wb_collarge_1 {
        -webkit-flex-basis: 8.333%;
        -ms-flex-preferred-size: 8.333%;
        flex-basis: 8.333%;
        max-width: 8.333%;
    }
    
    .wb_collarge_2 {
        -webkit-flex-basis: 16.667%;
        -ms-flex-preferred-size: 16.667%;
        flex-basis: 16.667%;
        max-width: 16.667%;
    }
    
    .wb_collarge_3 {
        -webkit-flex-basis: 25%;
        -ms-flex-preferred-size: 25%;
        flex-basis: 25%;
        max-width: 25%;
    }
    
    .wb_collarge_4 {
        -webkit-flex-basis: 33.333%;
        -ms-flex-preferred-size: 33.333%;
        flex-basis: 33.333%;
        max-width: 33.333%;
    }
    
    .wb_collarge_5 {
        -webkit-flex-basis: 41.667%;
        -ms-flex-preferred-size: 41.667%;
        flex-basis: 41.667%;
        max-width: 41.667%;
    }
    
    .wb_collarge_6 {
        -webkit-flex-basis: 50%;
        -ms-flex-preferred-size: 50%;
        flex-basis: 50%;
        max-width: 50%;
    }
    
    .wb_collarge_7 {
        -webkit-flex-basis: 58.333%;
        -ms-flex-preferred-size: 58.333%;
        flex-basis: 58.333%;
        max-width: 58.333%;
    }
    
    .wb_collarge_8 {
        -webkit-flex-basis: 66.667%;
        -ms-flex-preferred-size: 66.667%;
        flex-basis: 66.667%;
        max-width: 66.667%;
    }
    
    .wb_collarge_9 {
        -webkit-flex-basis: 75%;
        -ms-flex-preferred-size: 75%;
        flex-basis: 75%;
        max-width: 75%;
    }
    
    .wb_collarge_10 {
        -webkit-flex-basis: 83.333%;
        -ms-flex-preferred-size: 83.333%;
        flex-basis: 83.333%;
        max-width: 83.333%;
    }
    
    .wb_collarge_11 {
        -webkit-flex-basis: 91.667%;
        -ms-flex-preferred-size: 91.667%;
        flex-basis: 91.667%;
        max-width: 91.667%;
    }
    
    .wb_collarge_12 {
        -webkit-flex-basis: 100%;
        -ms-flex-preferred-size: 100%;
        flex-basis: 100%;
        max-width: 100%;
    }
    
    .wb_colorder_l1 {
        -webkit-order: 1;
        -ms-flex-order: 1;
        order: 1;
    }
    
    .wb_colorder_l2 {
        -webkit-order: 2;
        -ms-flex-order: 2;
        order: 2;
    }
    
    .wb_colorder_l3 {
        -webkit-order: 3;
        -ms-flex-order: 3;
        order: 3;
    }
    
    .wb_colorder_l4 {
        -webkit-order: 4;
        -ms-flex-order: 4;
        order: 4;
    }
    
    .wb_colorder_l5 {
        -webkit-order: 5;
        -ms-flex-order: 5;
        order: 5;
    }
    
    .wb_colorder_l6 {
        -webkit-order: 6;
        -ms-flex-order: 6;
        order: 6;
    }
    
    .wb_colorder_l7 {
        -webkit-order: 7;
        -ms-flex-order: 7;
        order: 7;
    }
    
    .wb_colorder_l8 {
        -webkit-order: 8;
        -ms-flex-order: 8;
        order: 8;
    }
    
    .wb_colorder_l9 {
        -webkit-order: 9;
        -ms-flex-order: 9;
        order: 9;
    }
    
    .wb_colorder_l10 {
        -webkit-order: 10;
        -ms-flex-order: 10;
        order: 10;
    }
    
    .wb_colorder_l11 {
        -webkit-order: 11;
        -ms-flex-order: 11;
        order: 11;
    }
    
    .wb_colorder_l12 {
        -webkit-order: 12;
        -ms-flex-order: 12;
        order: 12;
    }
}

