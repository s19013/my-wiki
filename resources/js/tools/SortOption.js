//タグを名前順でソート
export const sortArrayByName = (x,y) => {
    // 大文字小文字無視ソート
    x = x.name.toString().toLowerCase();
    y = y.name.toString().toLowerCase();
    if (x < y) {return -1;}
    if (x > y) {return 1;}
    return 0;
}

// export const add = (num1, num2) => {
//     return num1 + num2;
// };
