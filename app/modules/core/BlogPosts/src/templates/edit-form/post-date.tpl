<div class="field">
    <label for="post_date">Schedule post
        <a href="/" onclick="alert('Set the date and time for this post to show on your blog.'); return false;">[?]</a>
    </label>
    <div class="ui calendar" id="post_date_wrapper">
        <div class="ui input left icon">
            <i class="calendar icon"></i>
            <input type="text" name="post_date" id="post_date" autocomplete="off" placeholder="Date/Time" value="{$postdate}" class="post-data-field" data-key="date">
        </div>
    </div>
    <script>
    $('#post_date_wrapper').calendar({
        monthFirst: false,
        formatInput: true,
        formatter: {
            date: function (date, settings) {
                if (!date) return '';
                var day = '00' + date.getDate();
                day = day.substring(day.length - 2);
                var month = '00' + (date.getMonth() + 1)
                month = month.substring(month.length - 2);
                var year = date.getFullYear();

                return year + '-' + month + '-' + day;
            },
            time: function (date, settings, forCalendar) {
                if (!date) return '';
                var hour = '00' + date.getHours();
                var minute = '00' + date.getMinutes();
                hour = hour.substring(hour.length - 2);
                minute = minute.substring(minute.length - 2);

                return hour + ':' + minute;
            }
        }
    });
    </script>
</div>