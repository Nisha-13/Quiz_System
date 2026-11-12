@php
    $assetBase = $assetBase ?? '';
    $date = \Carbon\Carbon::parse($data['date']);
    $nameClass = mb_strlen($data['name']) > 26 ? 'cert-name-long' : '';
@endphp
<div class="cert-card">
    <table class="cert-frame" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td class="cert-edge"></td>
            <td class="cert-core">
                <div class="cert-header">
                    <div class="cert-brand">
                        <div class="cert-logo">
                            <img src="{{ $assetBase }}images/cert-logo.svg" alt="QuizSphere" class="cert-logo-mark">
                        </div>
                        <div class="cert-brand-text">
                            <div class="cert-brand-name">Quiz<span>Sphere</span></div>
                            <div class="cert-brand-sub">Official Certificate</div>
                        </div>
                    </div>
                </div>

                <x-gradient-bar from="#0d9488" via="#10b981" to="#0d9488" :steps="24" height="4" class="cert-accent" />

                <div class="cert-body">
                    <div class="cert-seal">
                        <img src="{{ $assetBase }}images/cert-award.svg" alt="" class="cert-seal-icon">
                    </div>

                    <div class="cert-kicker">This certifies that</div>
                    <div class="cert-name {{ $nameClass }}">{{ $data['name'] }}</div>
                    <x-gradient-bar from="#cbd5e1" via="#14b8a6" to="#cbd5e1" :steps="16" height="1" width="160px" class="cert-name-rule" />

                    <div class="cert-completes">has successfully completed the quiz</div>
                    <div class="cert-quiz">&ldquo;{{ $data['quiz'] }}&rdquo;</div>

                    <div class="cert-chips">
                        <span class="cert-chip cert-chip-score">
                            <img src="{{ $assetBase }}images/cert-check.svg" alt="" class="cert-chip-icon">
                            Score: {{ $data['score'] }}% ({{ $data['correct'] }}/{{ $data['total'] }})
                        </span>
                        <span class="cert-chip cert-chip-date">
                            <img src="{{ $assetBase }}images/cert-calendar.svg" alt="" class="cert-chip-icon">
                            {{ $date->format('M d, Y') }}
                        </span>
                    </div>

                    <div class="cert-quote">&ldquo;Learning is the key to success, and success begins with achievement.&rdquo;</div>

                    <table class="cert-signatures" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <div class="cert-script">Nisha</div>
                                <div class="cert-signature-line"></div>
                                <div class="cert-signature-label">Admin Signature</div>
                            </td>
                            <td>
                                <div class="cert-id">ID: QZ-{{ date('Y') }}-{{ str_pad($data['id'], 5, '0', STR_PAD_LEFT) }}</div>
                                <div class="cert-signature-line"></div>
                                <div class="cert-signature-label">Certificate ID</div>
                            </td>
                            <td>
                                <div class="cert-script">{{ $date->format('F d, Y') }}</div>
                                <div class="cert-signature-line"></div>
                                <div class="cert-signature-label">Date</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <x-gradient-bar from="#0d9488" via="#10b981" to="#0d9488" :steps="24" height="8" class="cert-footer" />
            </td>
            <td class="cert-edge"></td>
        </tr>
    </table>
</div>
