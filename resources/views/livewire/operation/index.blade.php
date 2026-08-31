<div class="space-y-6">

    {{-- ============================================================
        PAGE HEADER
    ============================================================= --}}

    <div>

        <h2
            class="text-2xl
                   font-semibold
                   tracking-tight
                   text-[#0b1c30]"
        >
            Operation Menu
        </h2>

        <p
            class="mt-1
                   text-sm
                   text-[#434655]"
        >
            Select a core function to begin or monitor tasks.
        </p>

    </div>


    {{-- ============================================================
        OPERATION MENU GRID
    ============================================================= --}}

    <div
        class="grid
               grid-cols-1
               gap-3
               sm:grid-cols-2
               lg:grid-cols-3
               xl:grid-cols-4
               auto-rows-fr"
    >


        {{-- ========================================================
            1. PICKUP
        ========================================================= --}}

        <a
            href="{{ route('operation.pickup') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#004ac6]"
            >

                <span
                    class="material-symbols-outlined text-[28px]"
                    style="font-variation-settings: 'FILL' 1;"
                >
                    local_shipping
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                PICKUP
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_pickups
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            2. BEHANDLE IN
        ========================================================= --}}

        <a
            href="{{ route('operation.behandle-in') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#006242]"
            >

                <span
                    class="material-symbols-outlined text-[28px]"
                    style="font-variation-settings: 'FILL' 1;"
                >
                    move_to_inbox
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                BEHANDLE IN
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_behandleins
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            3. HOLD
        ========================================================= --}}

        <a
            href="{{ route('operation.hold') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#ba1a1a]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#ba1a1a]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       items-start
                       justify-between"
            >

                <div
                    class="flex
                           h-12
                           w-12
                           items-center
                           justify-center
                           rounded-lg
                           bg-[#ffdad6]
                           text-[#ba1a1a]"
                >

                    <span
                        class="material-symbols-outlined text-[28px]"
                        style="font-variation-settings: 'FILL' 1;"
                    >
                        front_hand
                    </span>

                </div>


                <span
                    class="rounded
                           border
                           border-[#ba1a1a]/20
                           bg-[#ba1a1a]/10
                           px-2
                           py-1
                           text-[10px]
                           font-medium
                           text-[#ba1a1a]"
                >
                    Critical Alert
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                HOLD
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_holds
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-end
                       justify-between
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <div>

                    <span
                        class="block
                               text-xs
                               text-[#434655]"
                    >
                        Pending Clearance
                    </span>

                </div>


                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            4. MARSHALLING CIC
        ========================================================= --}}

        <a
            href="{{ route('operation.marshallingcic') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#0b1c30]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    warehouse
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                MARSHALLING CIC
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                marshalling_type = CIC
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            5. MARSHALLING YARD
        ========================================================= --}}

        <a
            href="{{ route('operation.marshalling-yard') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#0b1c30]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    crop_din
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                MARSHALLING YARD
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                marshalling_type = YARD
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            6. PEMERIKSAAN BEHANDLE
        ========================================================= --}}

        <a
            href="{{ route('operation.inspection') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#004ac6]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    fact_check
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                PEMERIKSAAN BEHANDLE
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_inspections
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            7. PLUG REEFER
        ========================================================= --}}

        <a
            href="{{ route('operation.plug-reefer') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#0ea5e9]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    power
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                PLUG REEFER
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_reefers
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            8. MONITORING REEFER
        ========================================================= --}}

        <a
            href="{{ route('operation.monitoring-reefer') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#565e74]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    thermostat
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                MONITORING REEFER
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                reefer_monitorings
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            9. DELIVERY
        ========================================================= --}}

        <a
            href="{{ route('operation.delivery') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#004ac6]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    outbound
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                DELIVERY
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_deliveries
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            10. INSPECTION OUT
        ========================================================= --}}

        <a
            href="{{ route('operation.inspection-out') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#006242]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    done_all
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                INSPECTION OUT
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_inspection_outs
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            11. ON CHASSIS
        ========================================================= --}}

        <a
            href="{{ route('operation.on-chassis') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#0b1c30]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    rv_hookup
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                ON CHASSIS
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_chassis
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>



        {{-- ========================================================
            12. COPY YARD
        ========================================================= --}}

        <a
            href="{{ route('operation.copy-yard') }}"
            class="group
                   relative
                   flex
                   h-full
                   flex-col
                   overflow-hidden
                   rounded-xl
                   border
                   border-[#c3c6d7]/30
                   bg-white
                   p-6
                   transition-all
                   duration-200
                   hover:border-[#004ac6]
                   hover:shadow-[0px_4px_12px_rgba(15,23,42,0.08)]"
        >

            <div
                class="absolute
                       inset-0
                       bg-[#004ac6]/5
                       opacity-0
                       transition-opacity
                       group-hover:opacity-100"
            ></div>


            <div
                class="relative
                       mb-4
                       flex
                       h-12
                       w-12
                       items-center
                       justify-center
                       rounded-lg
                       bg-[#d3e4fe]
                       text-[#0b1c30]"
            >

                <span class="material-symbols-outlined text-[28px]">
                    content_copy
                </span>

            </div>


            <h3
                class="relative
                       mb-1
                       text-base
                       font-semibold
                       text-[#0b1c30]
                       transition-colors
                       group-hover:text-[#004ac6]"
            >
                COPY YARD
            </h3>


            <p
                class="relative
                       text-[11px]
                       font-semibold
                       uppercase
                       tracking-wider
                       text-[#565e74]"
            >
                operation_copyyards
            </p>


            <div
                class="relative
                       mt-auto
                       flex
                       items-center
                       justify-end
                       border-t
                       border-[#c3c6d7]/20
                       pt-3"
            >

                <span
                    class="material-symbols-outlined
                           text-[20px]
                           text-[#737686]
                           transition-colors
                           group-hover:text-[#004ac6]"
                >
                    arrow_forward
                </span>

            </div>

        </a>

    </div>


    {{-- ============================================================
        FOOTER
    ============================================================= --}}

    <div
        class="border-t
               border-[#c3c6d7]/30
               pt-5
               text-center"
    >

        <p
            class="text-xs
                   text-[#737686]"
        >
            Showing 12 of 12 primary operations.
        </p>

    </div>

</div>