import { MODAL }          from "bootstrap"
import { get, post, del } from "./ajax"
import DataTable          from "datatables.net"

window.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.href
    const url        = new URL(currentUrl)
    const pathName   = url.pathname
    const categoryId = pathName.split('/').pop()
    console.log(categoryId)

    const table = new DataTable('#exercisesTable', {
        serverSide: true,
        ajax: `/exercises/load/${ categoryId }`,
        orderMulti: false,
        columns: [
            {data: "exerciseName"},
            {data: "description"}
        ]
    })
})