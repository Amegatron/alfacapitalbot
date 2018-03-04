<ul>
    @foreach($opifs as $opif)
        <li>
                {{ $opif->fullName }}<br/>
                <table cellpadding="10px">
                        <tr>
                                <td>{{ $opif->latestCourse()->course }}</td>
                                <td>{{ $opif->latestCourse()->course * $opif->my_amount }} руб.</td>
                        </tr>
                </table>
        </li>
    @endforeach
</ul>