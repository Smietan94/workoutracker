import { MODAL }          from "bootstrap"
import { get, post, del } from "./ajax"
import DataTable          from "datatables.net"

window.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.training-day-btn')

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            const tableId = button.getAttribute('data-target')
            const table   = document.getElementById(tableId)

            if (table.classList.contains('table-hidden')) {
                table.classList.remove('table-hidden')
            } else {
                table.classList.add('table-hidden')
            }
        })
    })

    
})




