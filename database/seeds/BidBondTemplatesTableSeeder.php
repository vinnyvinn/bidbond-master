<?php

use Illuminate\Database\Seeder;
use App\Services\BidBondTemplates;
use App\Models\BidBondTemplate;

class BidBondTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $content_1 = '<b class="body">
                <b>{{ $todays_date }}<br/>
                 Our ref: {{ $bidbond_reference }}<br/>
                @if($tender_addressee) TO: {{ $tender_addressee }},</br>@endif
                {{ $counter_party_name}},</br>
                P.O. BOX <span> {{ $counter_party_postal_address }}-{{ $counter_party_postal_code}}</span>,</br>
                {{ $counter_party_county }}, KENYA.</br>
                </b>
                <p class="greeting">Dear Sir/Madam,</p>
                <p style="text-align:center"><b><u>RE: BID BOND </u></b></p>
                <div class="content" style="font-weight: normal"><p>
                        WHEREAS  <b>{{ $company_name }} </b> of
                        <b>P.O. Box {{ $company_postal_address }}</b>
                        (hereinafter called "the Tenderer") has submitted its tender dated
                        <b>{{ $tender_effective_date }} </b> under Tender No. {{ $tender_number }}
                        for the <b>{{ $tender_purpose }} </b>(hereinafter called "The Tender");
                    </p> <p>
                        KNOW ALL PEOPLE by these presents that <b>WE JAMII BORA BANK LTD </b> of
                        <b>P.O. Box 25363-00603, Nairobi </b>having our registered office at
                        <span class="text-bold">Jamii Bora Towers, Argwings Kodhek Road, Kilimani, Nairobi, Kenya </span>
                        (hereinafter called “the Bank”) are bound unto <b>{{ $counter_party_name}}</b>
                        (hereinafter called "the Procuring entity"), in the sum of <b>KES. {{ $tender_amount_number }}
                            ({{ $tender_amount_words }} Only)</b>
                            for which payment well and truly to be made to the said Procuring Entity, the Bank binds itself, its successors and assigns by these presents.
                    </p> <p>Signed by the said Bank this<b> {{ $todays_date }}</b></p>
                    <div><p><b>THE CONDITIONS</b> of this obligation are:-</p>
                        <ol class="outer-list"><li>If the Tenderer withdraws its Tender during the period of Tender validity specified by the Procuring Entity on the Form; or </li>
                            <li>If the Tenderer, having been notified of the acceptance of its Tender by the Procuring Entity during the period of Tender validity:
                                <ol type="a" class="inner-list">
                                    <li>Fails or refuses to execute the Contract Form, if required; or</li>
                                    <li>Fails or refuses to furnish the Performance Security in accordance with the Instructions to Tenderers;</li>
                                 </ol>
                            </li>
                        </ol>
                    </div> <p>We undertake to pay the Procuring entity up to the above amount upon receipt of its first written demand without the Procuring entity having to substantiate its demand, provided that in its demand the Procuring entity will note that the amount claimed by it is due to it, owing to the occurrence of one or both of the two conditions, specifying the occurred condition or conditions.</p> <p>
                        This Guarantee is valid from {{ $tender_effective_date }}
                        and will remain in force up to and including the closure of business on
                        <span class="text-bold"> {{ $tender_expiry_date }} 3.00 p.m.</span> (expiry date and time).
                        Any demand in respect of this Guarantee should reach the Bank not later than the above expiry date and time.
                      </p>
                      <p>This guarantee is subject to the Uniform Rules for Demand Guarantees (URDG) 2010 Revision, ICC Publication No 758 and shall be governed and construed in accordance with the Laws of Kenya and the place of jurisdiction shall be Kenya.</p>
                    </div>
                </div>
        </div>';

        BidBondTemplates::store([
            'name' => 'Default',
            'content' => $content_1
        ]);

        $content_2 = '<div class="body">
                    <b>
                    TO: {{ $tender_addressee }},</br>
                    P.O. BOX <span> {{ $counter_party_postal_address }}-{{ $counter_party_postal_code }}</span>,</br>
                    {{ $counter_party_county }}, Kenya.
                </b>
                <p>
                    <b>Bid Bond No: {{ $bidbond_reference }}</b>
                </p>
                <div class="content" style="font-weight: normal">
                <p>
                        KNOW ALL PERSONS BY THESE PRESENTS, that we, <b> JAMII BORA BANK LTD  </b> of
                        <b>Nairobi,Kenya </b>having our registered office at
                        <b>P.O. Box 22741-00100, Nairobi, </b> organized under the laws of REPUBLIC OF KENYA and duly licensed or authorized to transact business in THE REPUBLIC OF KENYA as surety (hereinafter referred to as the “Surety”) are held and firmly bound unto the United Nations, an international intergovernmental organization, having its Headquarters in New York, NY 10017, U.S.A.,  as  obligee ,  in  the  amount  of
                        <b>Kes. {{ $tender_amount_number }} ({{ $tender_amount_words }} Only)</b> alphabetically and numerically, together with the applicable currency] (hereinafter referred to as the “Bond Value”), for the payment whereof which sum, well and truly to be made, the Surety bind itself, its successors, permitted assigns, executors and administrators, jointly and severally, firmly by these presents.
                    </p> <p>
                        WHEREAS, in response to the United Nations for {{ $tender_purpose }} (“Tender”), and pursuant to the solicitation documents issued by the the United Nations in connection with the Tender (“Solicitation Documents”),
                        <b>{{ $company_name }} </b>  of
                        <b>P.O. Box {{ $company_postal_address }} </b> a private limited company organized under the laws of the Republic of Kenya], as principal (hereinafter referred to as, the “Principal” or “Bidder”) has submitted a written Bid for {{ $tender_purpose }} to the the United Nations, dated the {{ $todays_date }} (hereinafter referred to as the “Bid”).
                    </p> <p>Sealed with the Common Seal of the said Bank this {{ $todays_date }}</p> <p>
                        NOW, THEREFORE, for valuable consideration,  the receipt and sufficiency   whereof is hereby acknowledged by the Surety, the Surety irrevocably undertakes the following:
                        <ol class="outer-list"><li>
                                The condition of this Bid Bond is such that if the Principal:
                                <ol class="inner-list">
                                <li>withdraws its Bid, without the consent of United Nations, during the period of Bid validity specified in Article 1 (b), below (Bid Bond Validity Period); or</li>
                                <li>having been notified of  the acceptance of  its Bid by the United Nations during the Bid Bond Validity Period, (a) unreasonably fails, delays or refuses, when required, in accordance with the terms specified in the Solicitation Documents, to execute the contract, or (b) fails, delays or refuses to furnish the Performance Bond or Labor and Materials Bond or any other bond in accordance with the terms specified in the Solicitation Documents;</li></ol>
                                <p>Then the Surety undertakes to immediately pay to the United Nations the Bond Value, upon first written demand by the United Nations, provided that in its demand the United Nations states that the demand arises from the occurrence of any of the above events, specifying which event(s) has occurred. The parties acknowledge and agree</p>
                                <p>that the Surety’s obligations under this Article 1 shall be enforceable without the need to have recourse to any judicial or arbitral proceedings and the Suretys obligation to pay the United Nations shall be fulfilled by the Surety without any objection, Opposition or recourse. The Surety and the Principal acknowledge and agree that the terms and conditions of the Bid Bond shall remain unchanged for the duration of the Bid Bond Validity Period. </p></li>
                                <li>The Surety hereby agrees that its obligation under the Bid Bond shall remain in full force and effect until such time as the United Nations notifies the Principal in writing that all of its obligations in relation to the Tender, as specified in the Solicitation Documents, have been fulfilled. Without prejudice to the foregoing, or limiting the generality of the foregoing, the Bid Bond shall remain in full force and effect for at least [30 days] following the expiration of the Bid Bond Validity Period specified in the Solicitation Documents (or any extensions thereof) or, otherwise, until such time that the United Nations has notified the Principal that the Bid Bond is no longer required and the United Nations has confirmed this fact in writing to the Surety upon request therefore. The Principal and the Surety acknowledge and agree that the United Nations may, at its sole discretion, extend the Bid Bond Validity Period prior to its expiration, notice of which extension(s) to the Surety being hereby waived.
                            </li> <li>
                                The parties acknowledge and agree that neither this Bid Bond nor any obligations hereunder are transferable or assignable. No right of action shall accrue on this Bid Bond to or for the use of any person or corporation other than the United Nations
                            </li> <li>
                                Nothing in or relating to this Bid Bond shall be deemed a waiver, express or implied, of any of the privileges or immunities of the United Nations, including its subsidiary organs.
                            </li> <li>
                                All notices required or contemplated under this Bid Bond shall be in writing and shall be delivered either by: (i) personal delivery; (ii) recognized overnight delivery service; and (iii) first-class, certified mail, return-receipt requested, and postage prepaid
                            </li></ol></p> <p>
                        This Guarantee will remain in force up to and including {{ $tender_effective_date }}.
                    </p> <p>
                        IN WITNESS WHEREOF, the authorized representatives of the parties have indicated their agreement to be firmly bound by these presents by having signed below on the date first written above:
                    </p> <p>
                        Upon expiry this Guarantee shall become null and void whether original is returned to us for cancellation or not and any claims or statement received after expiry date shall be ineffective.
                    </p></div></div></div>';

        BidBondTemplates::store([
            'name' => 'Another One',
            'content' => $content_2
        ]);
    }
}
