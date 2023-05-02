const JsBarcode = require("jsbarcode");
export default function showBarcode(product) {
    if (product) {
        let id = document.getElementById('singleBarcode')
        JsBarcode(id, product.barcode, {
            format: "pharmacode",
            font: 'Sans-serif',
            width: 1.7,
            fontSize: 15,
            height: 70,
            margin: 2,
            background: '#f9f9f9',
        });
    }
}
