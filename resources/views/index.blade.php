@php use Kima92\ExpectorPatronum\Enums\ExpectationStatus;use Kima92\ExpectorPatronum\Models\ExpectationPlan; @endphp
<?php
$balls = [
    ExpectationStatus::Pending->name => '<svg class="w-4 fill-current text-green-600 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
									</svg>',
    ExpectationStatus::Success->name => '<svg class="w-4 fill-current text-red-500 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
									</svg>',
    ExpectationStatus::Failed->name => '<svg class="w-4 fill-current text-gray-400 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
									</svg>',
    ExpectationStatus::SomeFailed->name => '<svg class="w-4 fill-current text-orange-400 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
										<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
									</svg>',
];
?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Expector Patronum</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>

    <link rel="stylesheet" href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" type="text/css"/>

    <script type="text/javascript" src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>
    <script src="https://unpkg.com/cronstrue@latest/dist/cronstrue.min.js"></script>
    <script src="https://unpkg.com/browse/cronstrue@2.47.0/dist/cronstrue-i18n.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/datepicker.min.js"></script>

    <style>
        #visualization {
            width: 100%;
            border: 1px solid lightgray;
        }

        .vis-item.vis-range {
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div style="width:100%;">
    <div id="visualization"></div>
    <div class="sm:px-6 w-full">
        <!--- more free and premium Tailwind CSS components at https://tailwinduikit.com/ --->
        <!--- https://tailwindcomponents.com/component/free-tailwind-css-advance-table-component --->

        <div class="bg-white py-4 md:py-7 px-4 md:px-8 xl:px-10">
            <div class="sm:flex items-center justify-between">
                <div date-rangepicker class="flex items-center">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input name="start" type="text" value="{{ $start->format("m/d/Y") }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date start">
                    </div>
                    <span class="mx-4 text-gray-500">to</span>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input name="end" type="text" value="{{ $end->format("m/d/Y") }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date end">
                    </div>

                    <button onclick="fetchData()" class="rounded-full mx-5 focus:outline-none focus:ring-2 py-2 px-8 bg-indigo-100 text-indigo-700 rounded-full hover:bg-indigo-50 hover:ring-indigo-800">
                        Filter
                    </button>
                </div>
            </div>
            <div class="mt-7 overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                    <tr tabindex="0" class="focus:outline-none h-12 border bg-slate-500 border-gray-100 rounded">
                        <th>
                            <div class="ml-5"></div>
                        </th>
                        <th class="">
                            <div class="flex items-center pl-5">
                                <p class="text-base font-medium leading-none text-white mr-2">ID</p>
                            </div>
                        </th>
                        <th class="pl-24">
                            <div class="flex items-center">
                                <p class="text-sm leading-none text-white ml-2">Name</p>
                            </div>
                        </th>
                        <th class="pl-5">
                            <div class="flex items-center">

                                <p class="text-sm leading-none text-white ml-2">Created</p>
                            </div>
                        </th>
                        <th class="pl-5">
                            <div class="flex items-center">

                                <p class="text-sm leading-none text-white ml-2">Schedule</p>
                            </div>
                        </th>
                        <th class="pl-5">
                            <div class="flex items-center">
                                <p class="text-sm leading-none text-white ml-2">Group</p>
                            </div>
                        </th>
                        <th class="pl-5">
                            <div class="flex items-center">
                                <p class="text-sm leading-none text-white ml-2">Rules</p>
                            </div>
                        </th>
                        <th class="pl-4">
                            <div class="flex items-center">
                                <p class="text-sm leading-none text-white ml-2">Last 5</p>
                            </div>
                        </th>
                    </tr>
                    <tr class="h-2"></tr>
                    </thead>
                    <tbody>

                    @foreach(ExpectationPlan::all() as $plan)
                        <tr tabindex="0" class="focus:outline-none h-12 border bg-slate-50  border-gray-100 rounded">
                            <td>
                                <div class="ml-5">
                                    <div
                                        class="bg-gray-200 rounded-sm w-5 h-5 flex flex-shrink-0 justify-center items-center relative">
                                        <input data-plan-id="{{$plan->id}}" placeholder="checkbox" type="checkbox" checked class="plan-checkbox focus:opacity-100 checkbox absolute cursor-pointer w-full h-full" onclick="updateTimeline()">
                                    </div>
                                </div>
                            </td>
                            <td class="">
                                <div class="flex items-center pl-5">
                                    <p class="text-base font-medium leading-none text-gray-700 mr-2">{{ $plan->id }}</p>
                                </div>
                            </td>
                            <td class="pl-24">
                                <div class="flex items-center">
                                    <p class="text-sm leading-none text-gray-600 ml-2">{{ $plan->name }}</p>
                                </div>
                            </td>
                            <td class="pl-5">
                                <div class="flex items-center">

                                    <p class="text-sm leading-none text-gray-600 ml-2">{{ $plan->created_at }}</p>
                                </div>
                            </td>
                            <td class="pl-5">
                                <div class="flex items-center">
                                    <p class="text-sm leading-none text-gray-600 ml-2"><span
                                            data-cron-exp="{{ $plan->schedule }}"></span><br>({{ $plan->schedule }})</p>
                                </div>
                            </td>
                            <td class="pl-5">
                                <div class="flex items-center">
                                    <p class="text-sm leading-none text-gray-600 ml-2">{{ $plan->group->name }}</p>
                                </div>
                            </td>
                            <td class="pl-5 flex flex-col">
                                @foreach($plan->rules as $rule)
                                    <span class="py-3 px-3 mt-2 text-sm focus:outline-none leading-none text-red-700 bg-red-100 rounded">
                                        <span class="font-bold">{{$rule["type"]}}: </span>
                                        @php unset($rule["type"]) @endphp
                                        {{ http_build_query($rule, arg_separator: ", ") }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="pl-4">
                                    <?php
                                    /** @var \Kima92\ExpectorPatronum\Models\Expectation $expectation */
                                    ?>
                                @foreach($plan->expectations()->where("expected_start_date", "<=", now())->latest()->take(5)->get() as $expectation)
                                    {!! $balls[$expectation->status->name] !!}
                                @endforeach
                            </td>
                        </tr>
                        <tr class="h-2"></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
</body>
<script>

    // Configuration for the Timeline
    var options = {
        start: '{{ $startFocus }}',
        end: '{{ $endFocus }}'
    };

    var items = [];
    var groups = [];
    var timeline;

    async function fetchData() {
        let start = formatDateToAPI(document.querySelector('input[name="start"]').value);
        let end = formatDateToAPI(document.querySelector('input[name="end"]').value);
        let data = await fetch("{{ config("expector-patronum.url") }}/items?start=" + start+"&end=" + end);
        data = await data.json();

        items = data["expected"].concat(data["reality"])
        groups = data["groups"]

        updateTimeline();
    }

    function formatDateToAPI(date) {
        let parts = date.split("/");

        let year = parts[2];
        let month = parts[0];
        let day = parts[1];

        return year + "-" + month + "-" + day;
    }

    fetchData();

    function updateTimeline() {
        let relevantGroups = Array.from(document.querySelectorAll('.plan-checkbox'))
            .filter((elm) => elm.checked)
            .map((elm) => parseInt(elm.getAttribute('data-plan-id')));

        console.log(relevantGroups)

        let filteredItems = items.filter((item) => relevantGroups.includes(item.plan_id))

        if (timeline) {
            timeline.destroy();
        }
        timeline = new vis.Timeline(document.getElementById('visualization'), new vis.DataSet(filteredItems), groups, options);
    }

    function waitForCronstrue(attemptsLeft) {
        if (typeof cronstrue !== 'undefined') {
            // cronstrue is loaded, run your code
            updateCronExpressions();
        } else if (attemptsLeft) {
            // cronstrue is not loaded yet, check again after a short delay
            setTimeout(() => waitForCronstrue(attemptsLeft - 1), 100);
        }
    }

    function updateCronExpressions() {
        // Select all elements with the class 'cronExp'
        var cronElements = document.querySelectorAll('[data-cron-exp]');

        cronElements.forEach(function (el) {
            // Get the cron expression from the data attribute

            var cronExpression = el.getAttribute('data-cron-exp');

            try {
                // Convert the cron expression to a readable string
                // Update the text of the td element
                el.textContent = cronstrue.toString(cronExpression, {
                    verbose: true,
                    use24HourTimeFormat: true,
                    locale: 'he'
                });
            } catch (e) {
                console.error("Error parsing cron expression: " + cronExpression, e);
                // Optionally handle the error, e.g., leave the original text or show an error message
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        waitForCronstrue(10);

        // document.querySelectorAll('.plan-checkbox').forEach((checkbox) => checkbox.checked = true)
    });


</script>
</html>
