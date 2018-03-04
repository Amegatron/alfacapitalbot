Последнее обновление: {{ $last_parse }}
<ul>
    @foreach($opifs as $opif)
        <li>
                {{ $opif->fullName }}<br/>
                <table cellpadding="10px">
                        <tr>
                                <td>{{ $opif->latestCourse()->course }} руб. ({{ $opif->latestCourse()->date }})</td>
                        </tr>
                </table>
        </li>
    @endforeach
</ul>