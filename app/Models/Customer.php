<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, \App\Traits\BelongsToShop;

    protected $fillable = [
        'shop_id', 'name', 'company_name', 'email', 'phone', 'address', 'city', 'zip', 'country', 'tax_id', 'vat_no', 'cr_no', 'is_active'
    ];

    public function companies()
    {
        return $this->hasMany(CustomerCompany::class, 'customer_id');
    }

    public function defaultCompany()
    {
        return $this->companies->firstWhere('is_default', true) 
            ?: $this->companies->first();
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Complete list of worldwide countries with ISO code, dial code, and name.
     * Saudi Arabia is strictly pinned as the #1 first and default country.
     */
    public static function allCountries(): array
    {
        return [
            // Pinned Top / Priority Countries
            'sa' => ['name' => 'Saudi Arabia', 'code' => '+966', 'iso' => 'sa'],
            'pk' => ['name' => 'Pakistan', 'code' => '+92', 'iso' => 'pk'],
            'ae' => ['name' => 'United Arab Emirates', 'code' => '+971', 'iso' => 'ae'],
            'om' => ['name' => 'Oman', 'code' => '+968', 'iso' => 'om'],
            'qa' => ['name' => 'Qatar', 'code' => '+974', 'iso' => 'qa'],
            'kw' => ['name' => 'Kuwait', 'code' => '+965', 'iso' => 'kw'],
            'bh' => ['name' => 'Bahrain', 'code' => '+973', 'iso' => 'bh'],
            'gb' => ['name' => 'United Kingdom', 'code' => '+44', 'iso' => 'gb'],
            'us' => ['name' => 'United States', 'code' => '+1', 'iso' => 'us'],
            'ca' => ['name' => 'Canada', 'code' => '+1', 'iso' => 'ca'],
            'in' => ['name' => 'India', 'code' => '+91', 'iso' => 'in'],
            'bd' => ['name' => 'Bangladesh', 'code' => '+880', 'iso' => 'bd'],
            'eg' => ['name' => 'Egypt', 'code' => '+20', 'iso' => 'eg'],
            'tr' => ['name' => 'Turkey', 'code' => '+90', 'iso' => 'tr'],

            // All Other Worldwide Countries Alphabetical
            'af' => ['name' => 'Afghanistan', 'code' => '+93', 'iso' => 'af'],
            'al' => ['name' => 'Albania', 'code' => '+355', 'iso' => 'al'],
            'dz' => ['name' => 'Algeria', 'code' => '+213', 'iso' => 'dz'],
            'as' => ['name' => 'American Samoa', 'code' => '+1684', 'iso' => 'as'],
            'ad' => ['name' => 'Andorra', 'code' => '+376', 'iso' => 'ad'],
            'ao' => ['name' => 'Angola', 'code' => '+244', 'iso' => 'ao'],
            'ai' => ['name' => 'Anguilla', 'code' => '+1264', 'iso' => 'ai'],
            'ag' => ['name' => 'Antigua and Barbuda', 'code' => '+1268', 'iso' => 'ag'],
            'ar' => ['name' => 'Argentina', 'code' => '+54', 'iso' => 'ar'],
            'am' => ['name' => 'Armenia', 'code' => '+374', 'iso' => 'am'],
            'aw' => ['name' => 'Aruba', 'code' => '+297', 'iso' => 'aw'],
            'au' => ['name' => 'Australia', 'code' => '+61', 'iso' => 'au'],
            'at' => ['name' => 'Austria', 'code' => '+43', 'iso' => 'at'],
            'az' => ['name' => 'Azerbaijan', 'code' => '+994', 'iso' => 'az'],
            'bs' => ['name' => 'Bahamas', 'code' => '+1242', 'iso' => 'bs'],
            'bb' => ['name' => 'Barbados', 'code' => '+1246', 'iso' => 'bb'],
            'by' => ['name' => 'Belarus', 'code' => '+375', 'iso' => 'by'],
            'be' => ['name' => 'Belgium', 'code' => '+32', 'iso' => 'be'],
            'bz' => ['name' => 'Belize', 'code' => '+501', 'iso' => 'bz'],
            'bj' => ['name' => 'Benin', 'code' => '+229', 'iso' => 'bj'],
            'bm' => ['name' => 'Bermuda', 'code' => '+1441', 'iso' => 'bm'],
            'bt' => ['name' => 'Bhutan', 'code' => '+975', 'iso' => 'bt'],
            'bo' => ['name' => 'Bolivia', 'code' => '+591', 'iso' => 'bo'],
            'ba' => ['name' => 'Bosnia and Herzegovina', 'code' => '+387', 'iso' => 'ba'],
            'bw' => ['name' => 'Botswana', 'code' => '+267', 'iso' => 'bw'],
            'br' => ['name' => 'Brazil', 'code' => '+55', 'iso' => 'br'],
            'bn' => ['name' => 'Brunei', 'code' => '+673', 'iso' => 'bn'],
            'bg' => ['name' => 'Bulgaria', 'code' => '+359', 'iso' => 'bg'],
            'bf' => ['name' => 'Burkina Faso', 'code' => '+226', 'iso' => 'bf'],
            'bi' => ['name' => 'Burundi', 'code' => '+257', 'iso' => 'bi'],
            'kh' => ['name' => 'Cambodia', 'code' => '+855', 'iso' => 'kh'],
            'cm' => ['name' => 'Cameroon', 'code' => '+237', 'iso' => 'cm'],
            'cv' => ['name' => 'Cape Verde', 'code' => '+238', 'iso' => 'cv'],
            'ky' => ['name' => 'Cayman Islands', 'code' => '+1345', 'iso' => 'ky'],
            'cf' => ['name' => 'Central African Republic', 'code' => '+236', 'iso' => 'cf'],
            'td' => ['name' => 'Chad', 'code' => '+235', 'iso' => 'td'],
            'cl' => ['name' => 'Chile', 'code' => '+56', 'iso' => 'cl'],
            'cn' => ['name' => 'China', 'code' => '+86', 'iso' => 'cn'],
            'co' => ['name' => 'Colombia', 'code' => '+57', 'iso' => 'co'],
            'km' => ['name' => 'Comoros', 'code' => '+269', 'iso' => 'km'],
            'cg' => ['name' => 'Congo', 'code' => '+242', 'iso' => 'cg'],
            'cd' => ['name' => 'DR Congo', 'code' => '+243', 'iso' => 'cd'],
            'ck' => ['name' => 'Cook Islands', 'code' => '+682', 'iso' => 'ck'],
            'cr' => ['name' => 'Costa Rica', 'code' => '+506', 'iso' => 'cr'],
            'ci' => ['name' => "Cote d'Ivoire", 'code' => '+225', 'iso' => 'ci'],
            'hr' => ['name' => 'Croatia', 'code' => '+385', 'iso' => 'hr'],
            'cu' => ['name' => 'Cuba', 'code' => '+53', 'iso' => 'cu'],
            'cy' => ['name' => 'Cyprus', 'code' => '+357', 'iso' => 'cy'],
            'cz' => ['name' => 'Czech Republic', 'code' => '+420', 'iso' => 'cz'],
            'dk' => ['name' => 'Denmark', 'code' => '+45', 'iso' => 'dk'],
            'dj' => ['name' => 'Djibouti', 'code' => '+253', 'iso' => 'dj'],
            'dm' => ['name' => 'Dominica', 'code' => '+1767', 'iso' => 'dm'],
            'do' => ['name' => 'Dominican Republic', 'code' => '+1809', 'iso' => 'do'],
            'ec' => ['name' => 'Ecuador', 'code' => '+593', 'iso' => 'ec'],
            'sv' => ['name' => 'El Salvador', 'code' => '+503', 'iso' => 'sv'],
            'gq' => ['name' => 'Equatorial Guinea', 'code' => '+240', 'iso' => 'gq'],
            'er' => ['name' => 'Eritrea', 'code' => '+291', 'iso' => 'er'],
            'ee' => ['name' => 'Estonia', 'code' => '+372', 'iso' => 'ee'],
            'sz' => ['name' => 'Eswatini', 'code' => '+268', 'iso' => 'sz'],
            'et' => ['name' => 'Ethiopia', 'code' => '+251', 'iso' => 'et'],
            'fo' => ['name' => 'Faroe Islands', 'code' => '+298', 'iso' => 'fo'],
            'fj' => ['name' => 'Fiji', 'code' => '+679', 'iso' => 'fj'],
            'fi' => ['name' => 'Finland', 'code' => '+358', 'iso' => 'fi'],
            'fr' => ['name' => 'France', 'code' => '+33', 'iso' => 'fr'],
            'gf' => ['name' => 'French Guiana', 'code' => '+594', 'iso' => 'gf'],
            'pf' => ['name' => 'French Polynesia', 'code' => '+689', 'iso' => 'pf'],
            'ga' => ['name' => 'Gabon', 'code' => '+241', 'iso' => 'ga'],
            'gm' => ['name' => 'Gambia', 'code' => '+220', 'iso' => 'gm'],
            'ge' => ['name' => 'Georgia', 'code' => '+995', 'iso' => 'ge'],
            'de' => ['name' => 'Germany', 'code' => '+49', 'iso' => 'de'],
            'gh' => ['name' => 'Ghana', 'code' => '+233', 'iso' => 'gh'],
            'gi' => ['name' => 'Gibraltar', 'code' => '+350', 'iso' => 'gi'],
            'gr' => ['name' => 'Greece', 'code' => '+30', 'iso' => 'gr'],
            'gl' => ['name' => 'Greenland', 'code' => '+299', 'iso' => 'gl'],
            'gd' => ['name' => 'Grenada', 'code' => '+1473', 'iso' => 'gd'],
            'gp' => ['name' => 'Guadeloupe', 'code' => '+590', 'iso' => 'gp'],
            'gu' => ['name' => 'Guam', 'code' => '+1671', 'iso' => 'gu'],
            'gt' => ['name' => 'Guatemala', 'code' => '+502', 'iso' => 'gt'],
            'gn' => ['name' => 'Guinea', 'code' => '+224', 'iso' => 'gn'],
            'gw' => ['name' => 'Guinea-Bissau', 'code' => '+245', 'iso' => 'gw'],
            'gy' => ['name' => 'Guyana', 'code' => '+592', 'iso' => 'gy'],
            'ht' => ['name' => 'Haiti', 'code' => '+509', 'iso' => 'ht'],
            'hn' => ['name' => 'Honduras', 'code' => '+504', 'iso' => 'hn'],
            'hk' => ['name' => 'Hong Kong', 'code' => '+852', 'iso' => 'hk'],
            'hu' => ['name' => 'Hungary', 'code' => '+36', 'iso' => 'hu'],
            'is' => ['name' => 'Iceland', 'code' => '+354', 'iso' => 'is'],
            'id' => ['name' => 'Indonesia', 'code' => '+62', 'iso' => 'id'],
            'ir' => ['name' => 'Iran', 'code' => '+98', 'iso' => 'ir'],
            'iq' => ['name' => 'Iraq', 'code' => '+964', 'iso' => 'iq'],
            'ie' => ['name' => 'Ireland', 'code' => '+353', 'iso' => 'ie'],
            'il' => ['name' => 'Israel', 'code' => '+972', 'iso' => 'il'],
            'it' => ['name' => 'Italy', 'code' => '+39', 'iso' => 'it'],
            'jm' => ['name' => 'Jamaica', 'code' => '+1876', 'iso' => 'jm'],
            'jp' => ['name' => 'Japan', 'code' => '+81', 'iso' => 'jp'],
            'jo' => ['name' => 'Jordan', 'code' => '+962', 'iso' => 'jo'],
            'kz' => ['name' => 'Kazakhstan', 'code' => '+7', 'iso' => 'kz'],
            'ke' => ['name' => 'Kenya', 'code' => '+254', 'iso' => 'ke'],
            'ki' => ['name' => 'Kiribati', 'code' => '+686', 'iso' => 'ki'],
            'xk' => ['name' => 'Kosovo', 'code' => '+383', 'iso' => 'xk'],
            'kg' => ['name' => 'Kyrgyzstan', 'code' => '+996', 'iso' => 'kg'],
            'la' => ['name' => 'Laos', 'code' => '+856', 'iso' => 'la'],
            'lv' => ['name' => 'Latvia', 'code' => '+371', 'iso' => 'lv'],
            'lb' => ['name' => 'Lebanon', 'code' => '+961', 'iso' => 'lb'],
            'ls' => ['name' => 'Lesotho', 'code' => '+266', 'iso' => 'ls'],
            'lr' => ['name' => 'Liberia', 'code' => '+231', 'iso' => 'lr'],
            'ly' => ['name' => 'Libya', 'code' => '+218', 'iso' => 'ly'],
            'li' => ['name' => 'Liechtenstein', 'code' => '+423', 'iso' => 'li'],
            'lt' => ['name' => 'Lithuania', 'code' => '+370', 'iso' => 'lt'],
            'lu' => ['name' => 'Luxembourg', 'code' => '+352', 'iso' => 'lu'],
            'mo' => ['name' => 'Macau', 'code' => '+853', 'iso' => 'mo'],
            'mg' => ['name' => 'Madagascar', 'code' => '+261', 'iso' => 'mg'],
            'mw' => ['name' => 'Malawi', 'code' => '+265', 'iso' => 'mw'],
            'my' => ['name' => 'Malaysia', 'code' => '+60', 'iso' => 'my'],
            'mv' => ['name' => 'Maldives', 'code' => '+960', 'iso' => 'mv'],
            'ml' => ['name' => 'Mali', 'code' => '+223', 'iso' => 'ml'],
            'mt' => ['name' => 'Malta', 'code' => '+356', 'iso' => 'mt'],
            'mh' => ['name' => 'Marshall Islands', 'code' => '+692', 'iso' => 'mh'],
            'mq' => ['name' => 'Martinique', 'code' => '+596', 'iso' => 'mq'],
            'mr' => ['name' => 'Mauritania', 'code' => '+222', 'iso' => 'mr'],
            'mu' => ['name' => 'Mauritius', 'code' => '+230', 'iso' => 'mu'],
            'mx' => ['name' => 'Mexico', 'code' => '+52', 'iso' => 'mx'],
            'fm' => ['name' => 'Micronesia', 'code' => '+691', 'iso' => 'fm'],
            'md' => ['name' => 'Moldova', 'code' => '+373', 'iso' => 'md'],
            'mc' => ['name' => 'Monaco', 'code' => '+377', 'iso' => 'mc'],
            'mn' => ['name' => 'Mongolia', 'code' => '+976', 'iso' => 'mn'],
            'me' => ['name' => 'Montenegro', 'code' => '+382', 'iso' => 'me'],
            'ms' => ['name' => 'Montserrat', 'code' => '+1664', 'iso' => 'ms'],
            'ma' => ['name' => 'Morocco', 'code' => '+212', 'iso' => 'ma'],
            'mz' => ['name' => 'Mozambique', 'code' => '+258', 'iso' => 'mz'],
            'mm' => ['name' => 'Myanmar', 'code' => '+95', 'iso' => 'mm'],
            'na' => ['name' => 'Namibia', 'code' => '+264', 'iso' => 'na'],
            'nr' => ['name' => 'Nauru', 'code' => '+674', 'iso' => 'nr'],
            'np' => ['name' => 'Nepal', 'code' => '+977', 'iso' => 'np'],
            'nl' => ['name' => 'Netherlands', 'code' => '+31', 'iso' => 'nl'],
            'nc' => ['name' => 'New Caledonia', 'code' => '+687', 'iso' => 'nc'],
            'nz' => ['name' => 'New Zealand', 'code' => '+64', 'iso' => 'nz'],
            'ni' => ['name' => 'Nicaragua', 'code' => '+505', 'iso' => 'ni'],
            'ne' => ['name' => 'Niger', 'code' => '+227', 'iso' => 'ne'],
            'ng' => ['name' => 'Nigeria', 'code' => '+234', 'iso' => 'ng'],
            'nu' => ['name' => 'Niue', 'code' => '+683', 'iso' => 'nu'],
            'kp' => ['name' => 'North Korea', 'code' => '+850', 'iso' => 'kp'],
            'mk' => ['name' => 'North Macedonia', 'code' => '+389', 'iso' => 'mk'],
            'no' => ['name' => 'Norway', 'code' => '+47', 'iso' => 'no'],
            'ps' => ['name' => 'Palestine', 'code' => '+970', 'iso' => 'ps'],
            'pa' => ['name' => 'Panama', 'code' => '+507', 'iso' => 'pa'],
            'pg' => ['name' => 'Papua New Guinea', 'code' => '+675', 'iso' => 'pg'],
            'py' => ['name' => 'Paraguay', 'code' => '+595', 'iso' => 'py'],
            'pe' => ['name' => 'Peru', 'code' => '+51', 'iso' => 'pe'],
            'ph' => ['name' => 'Philippines', 'code' => '+63', 'iso' => 'ph'],
            'pl' => ['name' => 'Poland', 'code' => '+48', 'iso' => 'pl'],
            'pt' => ['name' => 'Portugal', 'code' => '+351', 'iso' => 'pt'],
            'pr' => ['name' => 'Puerto Rico', 'code' => '+1787', 'iso' => 'pr'],
            're' => ['name' => 'Reunion', 'code' => '+262', 'iso' => 're'],
            'ro' => ['name' => 'Romania', 'code' => '+40', 'iso' => 'ro'],
            'ru' => ['name' => 'Russia', 'code' => '+7', 'iso' => 'ru'],
            'rw' => ['name' => 'Rwanda', 'code' => '+250', 'iso' => 'rw'],
            'ws' => ['name' => 'Samoa', 'code' => '+685', 'iso' => 'ws'],
            'sm' => ['name' => 'San Marino', 'code' => '+378', 'iso' => 'sm'],
            'st' => ['name' => 'Sao Tome and Principe', 'code' => '+239', 'iso' => 'st'],
            'sn' => ['name' => 'Senegal', 'code' => '+221', 'iso' => 'sn'],
            'rs' => ['name' => 'Serbia', 'code' => '+381', 'iso' => 'rs'],
            'sc' => ['name' => 'Seychelles', 'code' => '+248', 'iso' => 'sc'],
            'sl' => ['name' => 'Sierra Leone', 'code' => '+232', 'iso' => 'sl'],
            'sg' => ['name' => 'Singapore', 'code' => '+65', 'iso' => 'sg'],
            'sk' => ['name' => 'Slovakia', 'code' => '+421', 'iso' => 'sk'],
            'si' => ['name' => 'Slovenia', 'code' => '+386', 'iso' => 'si'],
            'sb' => ['name' => 'Solomon Islands', 'code' => '+677', 'iso' => 'sb'],
            'so' => ['name' => 'Somalia', 'code' => '+252', 'iso' => 'so'],
            'za' => ['name' => 'South Africa', 'code' => '+27', 'iso' => 'za'],
            'kr' => ['name' => 'South Korea', 'code' => '+82', 'iso' => 'kr'],
            'ss' => ['name' => 'South Sudan', 'code' => '+211', 'iso' => 'ss'],
            'es' => ['name' => 'Spain', 'code' => '+34', 'iso' => 'es'],
            'lk' => ['name' => 'Sri Lanka', 'code' => '+94', 'iso' => 'lk'],
            'sd' => ['name' => 'Sudan', 'code' => '+249', 'iso' => 'sd'],
            'sr' => ['name' => 'Suriname', 'code' => '+597', 'iso' => 'sr'],
            'se' => ['name' => 'Sweden', 'code' => '+46', 'iso' => 'se'],
            'ch' => ['name' => 'Switzerland', 'code' => '+41', 'iso' => 'ch'],
            'sy' => ['name' => 'Syria', 'code' => '+963', 'iso' => 'sy'],
            'tw' => ['name' => 'Taiwan', 'code' => '+886', 'iso' => 'tw'],
            'tj' => ['name' => 'Tajikistan', 'code' => '+992', 'iso' => 'tj'],
            'tz' => ['name' => 'Tanzania', 'code' => '+255', 'iso' => 'tz'],
            'th' => ['name' => 'Thailand', 'code' => '+66', 'iso' => 'th'],
            'tl' => ['name' => 'Timor-Leste', 'code' => '+670', 'iso' => 'tl'],
            'tg' => ['name' => 'Togo', 'code' => '+228', 'iso' => 'tg'],
            'to' => ['name' => 'Tonga', 'code' => '+676', 'iso' => 'to'],
            'tt' => ['name' => 'Trinidad and Tobago', 'code' => '+1868', 'iso' => 'tt'],
            'tn' => ['name' => 'Tunisia', 'code' => '+216', 'iso' => 'tn'],
            'tm' => ['name' => 'Turkmenistan', 'code' => '+993', 'iso' => 'tm'],
            'tc' => ['name' => 'Turks and Caicos Islands', 'code' => '+1649', 'iso' => 'tc'],
            'tv' => ['name' => 'Tuvalu', 'code' => '+688', 'iso' => 'tv'],
            'ug' => ['name' => 'Uganda', 'code' => '+256', 'iso' => 'ug'],
            'ua' => ['name' => 'Ukraine', 'code' => '+380', 'iso' => 'ua'],
            'uy' => ['name' => 'Uruguay', 'code' => '+598', 'iso' => 'uy'],
            'uz' => ['name' => 'Uzbekistan', 'code' => '+998', 'iso' => 'uz'],
            'vu' => ['name' => 'Vanuatu', 'code' => '+678', 'iso' => 'vu'],
            've' => ['name' => 'Venezuela', 'code' => '+58', 'iso' => 've'],
            'vn' => ['name' => 'Vietnam', 'code' => '+84', 'iso' => 'vn'],
            'ye' => ['name' => 'Yemen', 'code' => '+967', 'iso' => 'ye'],
            'zm' => ['name' => 'Zambia', 'code' => '+260', 'iso' => 'zm'],
            'zw' => ['name' => 'Zimbabwe', 'code' => '+263', 'iso' => 'zw'],
        ];
    }

    /**
     * Backward-compatible alias.
     */
    public static function supportedCountries(): array
    {
        return self::allCountries();
    }

    /**
     * Normalize a phone number to standard international format (+<country_code><digits>).
     */
    public static function normalizePhone(?string $phone, ?string $countryCode = '+966'): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = trim($phone);

        // Normalize country code
        $countryCode = $countryCode ? trim($countryCode) : '+966';
        if (!str_starts_with($countryCode, '+')) {
            $countryCode = '+' . $countryCode;
        }

        // If user already typed leading '+' with country code
        if (str_starts_with($phone, '+')) {
            $digits = preg_replace('/[^\d]/', '', substr($phone, 1));
            return $digits ? '+' . $digits : null;
        }

        // Extract digits only
        $digits = preg_replace('/[^\d]/', '', $phone);
        if (empty($digits)) {
            return null;
        }

        $codeDigits = ltrim($countryCode, '+');

        // If user typed the country code digits without the '+' sign
        if (str_starts_with($digits, $codeDigits)) {
            return '+' . $digits;
        }

        // Strip leading 0 from local national numbers (e.g. 050... or 0301...)
        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
        }

        return $countryCode . $digits;
    }

    /**
     * Parse full phone string into country code, iso, and local number for edit forms.
     */
    public static function parsePhone(?string $fullPhone): array
    {
        $default = ['country_code' => '+966', 'iso' => 'sa', 'number' => ''];
        if (empty($fullPhone)) {
            return $default;
        }

        $cleaned = trim($fullPhone);
        $normalized = (str_starts_with($cleaned, '+') ? '+' : '') . preg_replace('/[^\d]/', '', $cleaned);

        $countries = self::allCountries();
        // Sort by dial code length descending so longer prefixes match first (e.g. +1242 before +1)
        uasort($countries, fn($a, $b) => strlen($b['code']) <=> strlen($a['code']));

        foreach ($countries as $iso => $c) {
            if (str_starts_with($normalized, $c['code'])) {
                return [
                    'country_code' => $c['code'],
                    'iso' => $iso,
                    'number' => substr($normalized, strlen($c['code'])),
                ];
            }
        }

        return [
            'country_code' => '+966',
            'iso' => 'sa',
            'number' => ltrim($normalized, '+'),
        ];
    }

    /**
     * Formatted display string for phone numbers.
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (!$phone) {
            return '';
        }

        if (preg_match('/^\+966(\d{2})(\d{3})(\d{4})$/', $phone, $m)) {
            return "+966 {$m[1]} {$m[2]} {$m[3]}";
        }
        if (preg_match('/^\+92(\d{3})(\d{7})$/', $phone, $m)) {
            return "+92 {$m[1]} {$m[2]}";
        }
        if (preg_match('/^\+971(\d{2})(\d{3})(\d{4})$/', $phone, $m)) {
            return "+971 {$m[1]} {$m[2]} {$m[3]}";
        }

        return $phone;
    }
}
