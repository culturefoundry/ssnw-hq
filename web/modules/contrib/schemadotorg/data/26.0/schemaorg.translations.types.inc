<?php

/**
 * @file
 * Translate Schema.org types id and comment strings.
 *
 * Passing Schema.org label and comments to Drupal's t() function to
 * ensure all translatable Schema.org strings discovered by
 * localize.drupal.org.
 *
 * DO NOT EDIT.
 * This file is generated using `drush schemadotorg:translate-schema`.
 *
 * @see https://localize.drupal.org/translate/languages/en-gb/translate?project=schemadotorg
 */

declare(strict_types=1);

// phpcs:disable Drupal.Semantics.FunctionT.BackslashSingleQuote

// https://schema.org/about
t("about");
t("The subject matter of the content.");

// https://schema.org/abridged
t("abridged");
t("Indicates whether the book is an abridged edition.");

// https://schema.org/abstract
t("abstract");
t('An abstract is a short description that summarizes a <a class="localLink" href="/CreativeWork">CreativeWork</a>.');

// https://schema.org/accelerationTime
t("accelerationTime");
t('The time needed to accelerate the vehicle from a given start velocity to a given target velocity.<br/><br/>

Typical unit code(s): SEC for seconds<br/><br/>

<ul>
<li>Note: There are unfortunately no standard unit codes for seconds/0..100 km/h or seconds/0..60 mph. Simply use "SEC" for seconds and indicate the velocities in the <a class="localLink" href="/name">name</a> of the <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a>, or use <a class="localLink" href="/valueReference">valueReference</a> with a <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> of 0..60 mph or 0..100 km/h to specify the reference speeds.</li>
</ul>');
t("The time needed to accelerate the vehicle from a given start velocity to a given target velocity.");

// https://schema.org/acceptedAnswer
t("acceptedAnswer");
t("The answer(s) that has been accepted as best, typically on a Question/Answer site. Sites vary in their selection mechanisms, e.g. drawing on community opinion and/or the view of the Question author.");
t("The answer(s) that has been accepted as best, typically on a Question/Answer site.");

// https://schema.org/acceptedOffer
t("acceptedOffer");
t("The offer(s) -- e.g., product, quantity and price combinations -- included in the order.");

// https://schema.org/acceptedPaymentMethod
t("acceptedPaymentMethod");
t("The payment method(s) accepted by seller for this offer.");

// https://schema.org/acceptsReservations
t("acceptsReservations");
t("Indicates whether a FoodEstablishment accepts reservations. Values can be Boolean, an URL at which reservations can be made or (for backwards compatibility) the strings <code>Yes</code> or <code>No</code>.");
t("Indicates whether a FoodEstablishment accepts reservations.");

// https://schema.org/accessCode
t("accessCode");
t("Password, PIN, or access code needed for delivery (e.g. from a locker).");

// https://schema.org/accessibilityAPI
t("accessibilityAPI");
t('Indicates that the resource is compatible with the referenced accessibility API. Values should be drawn from the <a href="https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityAPI-vocabulary">approved vocabulary</a>.');
t("Indicates that the resource is compatible with the referenced accessibility API.");

// https://schema.org/accessibilityControl
t("accessibilityControl");
t('Identifies input methods that are sufficient to fully control the described resource. Values should be drawn from the <a href="https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityControl-vocabulary">approved vocabulary</a>.');
t("Identifies input methods that are sufficient to fully control the described resource.");

// https://schema.org/accessibilityFeature
t("accessibilityFeature");
t('Content features of the resource, such as accessible media, alternatives and supported enhancements for accessibility. Values should be drawn from the <a href="https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityFeature-vocabulary">approved vocabulary</a>.');
t("Content features of the resource, such as accessible media, alternatives and supported enhancements for accessibility.");

// https://schema.org/accessibilityHazard
t("accessibilityHazard");
t('A characteristic of the described resource that is physiologically dangerous to some users. Related to WCAG 2.0 guideline 2.3. Values should be drawn from the <a href="https://www.w3.org/2021/a11y-discov-vocab/latest/#accessibilityHazard-vocabulary">approved vocabulary</a>.');
t("A characteristic of the described resource that is physiologically dangerous to some users.");

// https://schema.org/accessibilitySummary
t("accessibilitySummary");
t('A human-readable summary of specific accessibility features or deficiencies, consistent with the other accessibility metadata but expressing subtleties such as "short descriptions are present but long descriptions will be needed for non-visual users" or "short descriptions are present and no long descriptions are needed".');

// https://schema.org/accessMode
t("accessMode");
t('The human sensory perceptual system or cognitive faculty through which a person may process or perceive information. Values should be drawn from the <a href="https://www.w3.org/2021/a11y-discov-vocab/latest/#accessMode-vocabulary">approved vocabulary</a>.');
t("The human sensory perceptual system or cognitive faculty through which a person may process or perceive information.");

// https://schema.org/accessModeSufficient
t("accessModeSufficient");
t('A list of single or combined accessModes that are sufficient to understand all the intellectual content of a resource. Values should be drawn from the <a href="https://www.w3.org/2021/a11y-discov-vocab/latest/#accessModeSufficient-vocabulary">approved vocabulary</a>.');
t("A list of single or combined accessModes that are sufficient to understand all the intellectual content of a resource.");

// https://schema.org/accommodationCategory
t("accommodationCategory");
t('Category of an <a class="localLink" href="/Accommodation">Accommodation</a>, following real estate conventions, e.g. RESO (see <a href="https://ddwiki.reso.org/display/DDW17/PropertySubType+Field">PropertySubType</a>, and <a href="https://ddwiki.reso.org/display/DDW17/PropertyType+Field">PropertyType</a> fields  for suggested values).');

// https://schema.org/accommodationFloorPlan
t("accommodationFloorPlan");
t('A floorplan of some <a class="localLink" href="/Accommodation">Accommodation</a>.');

// https://schema.org/accountablePerson
t("accountablePerson");
t("Specifies the Person that is legally accountable for the CreativeWork.");

// https://schema.org/accountId
t("accountId");
t("The identifier for the account the payment will be applied to.");

// https://schema.org/accountMinimumInflow
t("accountMinimumInflow");
t("A minimum amount that has to be paid in every month.");

// https://schema.org/accountOverdraftLimit
t("accountOverdraftLimit");
t("An overdraft is an extension of credit from a lending institution when an account reaches zero. An overdraft allows the individual to continue withdrawing money even if the account has no funds in it. Basically the bank allows people to borrow a set amount of money.");
t("An overdraft is an extension of credit from a lending institution when an account reaches zero.");

// https://schema.org/acquiredFrom
t("acquiredFrom");
t("The organization or person from which the product was acquired.");

// https://schema.org/acquireLicensePage
t("acquireLicensePage");
t("Indicates a page documenting how licenses can be purchased or otherwise acquired, for the current item.");

// https://schema.org/acrissCode
t("acrissCode");
t("The ACRISS Car Classification Code is a code used by many car rental companies, for classifying vehicles. ACRISS stands for Association of Car Rental Industry Systems and Standards.");
t("The ACRISS Car Classification Code is a code used by many car rental companies, for classifying vehicles.");

// https://schema.org/actionableFeedbackPolicy
t("actionableFeedbackPolicy");
t('For a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a> or other news-related <a class="localLink" href="/Organization">Organization</a>, a statement about public engagement activities (for news media, the newsroom’s), including involving the public - digitally or otherwise -- in coverage decisions, reporting and activities after publication.');

// https://schema.org/actionAccessibilityRequirement
t("actionAccessibilityRequirement");
t("A set of requirements that must be fulfilled in order to perform an Action. If more than one value is specified, fulfilling one set of requirements will allow the Action to be performed.");
t("A set of requirements that must be fulfilled in order to perform an Action.");

// https://schema.org/actionApplication
t("actionApplication");
t("An application that can complete the request.");

// https://schema.org/actionOption
t("actionOption");
t("A sub property of object. The options subject to this action.");
t("A sub property of object.");

// https://schema.org/actionPlatform
t("actionPlatform");
t("The high level platform(s) where the Action can be performed for the given URL. To specify a specific application or operating system instance, use actionApplication.");
t("The high level platform(s) where the Action can be performed for the given URL.");

// https://schema.org/actionStatus
t("actionStatus");
t("Indicates the current disposition of the Action.");

// https://schema.org/activeIngredient
t("activeIngredient");
t("An active ingredient, typically chemical compounds and/or biologic substances.");

// https://schema.org/activityDuration
t("activityDuration");
t("Length of time to engage in the activity.");

// https://schema.org/activityFrequency
t("activityFrequency");
t("How often one should engage in the activity.");

// https://schema.org/actor
t("actor");
t("An actor, e.g. in TV, radio, movie, video games etc., or in an event. Actors can be associated with individual items or with a series, episode, clip.");
t("An actor, e.g. in TV, radio, movie, video games etc., or in an event.");

// https://schema.org/actors
t("actors");
t("An actor, e.g. in TV, radio, movie, video games etc. Actors can be associated with individual items or with a series, episode, clip.");

// https://schema.org/additionalName
t("additionalName");
t("An additional name for a Person, can be used for a middle name.");

// https://schema.org/additionalNumberOfGuests
t("additionalNumberOfGuests");
t("If responding yes, the number of guests who will attend in addition to the invitee.");

// https://schema.org/additionalProperty
t("additionalProperty");
t("A property-value pair representing an additional characteristic of the entity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.<br/><br/>

Note: Publishers should be aware that applications designed to use specific schema.org properties (e.g. https://schema.org/width, https://schema.org/color, https://schema.org/gtin13, ...) will typically expect such data to be provided using those properties, rather than using the generic property/value mechanism.");
t("A property-value pair representing an additional characteristic of the entity, e.g. a product feature or another characteristic for which there is no matching property in schema.org.");

// https://schema.org/additionalType
t("additionalType");
t('An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax. This is a relationship between something and a class that the thing is in. Typically the value is a URI-identified RDF class, and in this case corresponds to the
    use of rdf:type in RDF. Text values can be used sparingly, for cases where useful information can be added without their being an appropriate schema to reference. In the case of text values, the class label should follow the schema.org <a href="https://schema.org/docs/styleguide.html">style guide</a>.');
t("An additional type for the item, typically used for adding more specific types from external vocabularies in microdata syntax.");

// https://schema.org/additionalVariable
t("additionalVariable");
t("Any additional component of the exercise prescription that may need to be articulated to the patient. This may include the order of exercises, the number of repetitions of movement, quantitative distance, progressions over time, etc.");
t("Any additional component of the exercise prescription that may need to be articulated to the patient.");

// https://schema.org/addOn
t("addOn");
t("An additional offer that can only be obtained in combination with the first base offer (e.g. supplements and extensions that are available for a surcharge).");

// https://schema.org/address
t("address");
t("Physical address of the item.");

// https://schema.org/addressCountry
t("addressCountry");
t('The country. For example, USA. You can also provide the two-letter <a href="http://en.wikipedia.org/wiki/ISO_3166-1">ISO 3166-1 alpha-2 country code</a>.');
t("The country.");

// https://schema.org/addressLocality
t("addressLocality");
t("The locality in which the street address is, and which is in the region. For example, Mountain View.");
t("The locality in which the street address is, and which is in the region.");

// https://schema.org/addressRegion
t("addressRegion");
t('The region in which the locality is, and which is in the country. For example, California or another appropriate first-level <a href="https://en.wikipedia.org/wiki/List_of_administrative_divisions_by_country">Administrative division</a>.');
t("The region in which the locality is, and which is in the country.");

// https://schema.org/administrationRoute
t("administrationRoute");
t("A route by which this drug may be administered, e.g. 'oral'.");

// https://schema.org/advanceBookingRequirement
t("advanceBookingRequirement");
t("The amount of time that is required between accepting the offer and the actual usage of the resource or service.");

// https://schema.org/adverseOutcome
t("adverseOutcome");
t("A possible complication and/or side effect of this therapy. If it is known that an adverse outcome is serious (resulting in death, disability, or permanent damage; requiring hospitalization; or otherwise life-threatening or requiring immediate medical attention), tag it as a seriousAdverseOutcome instead.");
t("A possible complication and/or side effect of this therapy.");

// https://schema.org/affectedBy
t("affectedBy");
t("Drugs that affect the test's results.");

// https://schema.org/affiliation
t("affiliation");
t("An organization that this person is affiliated with. For example, a school/university, a club, or a team.");
t("An organization that this person is affiliated with.");

// https://schema.org/afterMedia
t("afterMedia");
t("A media object representing the circumstances after performing this direction.");

// https://schema.org/agent
t("agent");
t("The direct performer or driver of the action (animate or inanimate). E.g. <em>John</em> wrote a book.");
t("The direct performer or driver of the action (animate or inanimate).");

// https://schema.org/agentInteractionStatistic
t("agentInteractionStatistic");
t("The number of completed interactions for this entity, in a particular role (the 'agent'), in a particular action (indicated in the statistic), and in a particular context (i.e. interactionService).");

// https://schema.org/aggregateRating
t("aggregateRating");
t("The overall rating, based on a collection of reviews or ratings, of the item.");

// https://schema.org/aircraft
t("aircraft");
t('The kind of aircraft (e.g., "Boeing 747").');

// https://schema.org/album
t("album");
t("A music album.");

// https://schema.org/albumProductionType
t("albumProductionType");
t("Classification of the album by its type of content: soundtrack, live album, studio album, etc.");

// https://schema.org/albumRelease
t("albumRelease");
t("A release of this album.");

// https://schema.org/albumReleaseType
t("albumReleaseType");
t("The kind of release which this album is: single, EP or album.");

// https://schema.org/albums
t("albums");
t("A collection of music albums.");

// https://schema.org/alcoholWarning
t("alcoholWarning");
t("Any precaution, guidance, contraindication, etc. related to consumption of alcohol while taking this drug.");

// https://schema.org/algorithm
t("algorithm");
t("The algorithm or rules to follow to compute the score.");

// https://schema.org/alignmentType
t("alignmentType");
t("A category of alignment between the learning resource and the framework node. Recommended values include: 'requires', 'textComplexity', 'readingLevel', and 'educationalSubject'.");
t("A category of alignment between the learning resource and the framework node.");

// https://schema.org/alternateName
t("alternateName");
t("An alias for the item.");

// https://schema.org/alternativeHeadline
t("alternativeHeadline");
t("A secondary title of the CreativeWork.");

// https://schema.org/alternativeOf
t("alternativeOf");
t("Another gene which is a variation of this one.");

// https://schema.org/alumni
t("alumni");
t("Alumni of an organization.");

// https://schema.org/alumniOf
t("alumniOf");
t("An organization that the person is an alumni of.");

// https://schema.org/amenityFeature
t("amenityFeature");
t("An amenity feature (e.g. a characteristic or service) of the Accommodation. This generic property does not make a statement about whether the feature is included in an offer for the main accommodation or available at extra costs.");
t("An amenity feature (e.g. a characteristic or service) of the Accommodation.");

// https://schema.org/amount
t("amount");
t("The amount of money.");

// https://schema.org/amountOfThisGood
t("amountOfThisGood");
t("The quantity of the goods included in the offer.");

// https://schema.org/announcementLocation
t("announcementLocation");
t('Indicates a specific <a class="localLink" href="/CivicStructure">CivicStructure</a> or <a class="localLink" href="/LocalBusiness">LocalBusiness</a> associated with the SpecialAnnouncement. For example, a specific testing facility or business with special opening hours. For a larger geographic region like a quarantine of an entire region, use <a class="localLink" href="/spatialCoverage">spatialCoverage</a>.');
t('Indicates a specific <a class="localLink" href="/CivicStructure">CivicStructure</a> or <a class="localLink" href="/LocalBusiness">LocalBusiness</a> associated with the SpecialAnnouncement.');

// https://schema.org/annualPercentageRate
t("annualPercentageRate");
t("The annual rate that is charged for borrowing (or made by investing), expressed as a single percentage number that represents the actual yearly cost of funds over the term of a loan. This includes any fees or additional costs associated with the transaction.");
t("The annual rate that is charged for borrowing (or made by investing), expressed as a single percentage number that represents the actual yearly cost of funds over the term of a loan.");

// https://schema.org/answerCount
t("answerCount");
t("The number of answers this question has received.");

// https://schema.org/answerExplanation
t("answerExplanation");
t("A step-by-step or full explanation about Answer. Can outline how this Answer was achieved or contain more broad clarification or statement about it.");
t("A step-by-step or full explanation about Answer.");

// https://schema.org/antagonist
t("antagonist");
t("The muscle whose action counteracts the specified muscle.");

// https://schema.org/appearance
t("appearance");
t('Indicates an occurrence of a <a class="localLink" href="/Claim">Claim</a> in some <a class="localLink" href="/CreativeWork">CreativeWork</a>.');

// https://schema.org/applicableCountry
t("applicableCountry");
t("A country where a particular merchant return policy applies to, for example the two-letter ISO 3166-1 alpha-2 country code.");

// https://schema.org/applicableLocation
t("applicableLocation");
t("The location in which the status applies.");

// https://schema.org/applicantLocationRequirements
t("applicantLocationRequirements");
t("The location(s) applicants can apply from. This is usually used for telecommuting jobs where the applicant does not need to be in a physical office. Note: This should not be used for citizenship or work visa requirements.");
t("The location(s) applicants can apply from.");

// https://schema.org/application
t("application");
t("An application that can complete the request.");

// https://schema.org/applicationCategory
t("applicationCategory");
t("Type of software application, e.g. 'Game, Multimedia'.");

// https://schema.org/applicationContact
t("applicationContact");
t("Contact details for further information relevant to this job posting.");

// https://schema.org/applicationDeadline
t("applicationDeadline");
t("The date at which the program stops collecting applications for the next enrollment cycle.");

// https://schema.org/applicationStartDate
t("applicationStartDate");
t("The date at which the program begins collecting applications for the next enrollment cycle.");

// https://schema.org/applicationSubCategory
t("applicationSubCategory");
t("Subcategory of the application, e.g. 'Arcade Game'.");

// https://schema.org/applicationSuite
t("applicationSuite");
t("The name of the application suite to which the application belongs (e.g. Excel belongs to Office).");

// https://schema.org/appliesToDeliveryMethod
t("appliesToDeliveryMethod");
t("The delivery method(s) to which the delivery charge or payment charge specification applies.");

// https://schema.org/appliesToPaymentMethod
t("appliesToPaymentMethod");
t("The payment method(s) to which the payment charge specification applies.");

// https://schema.org/archivedAt
t("archivedAt");
t('Indicates a page or other link involved in archival of a <a class="localLink" href="/CreativeWork">CreativeWork</a>. In the case of <a class="localLink" href="/MediaReview">MediaReview</a>, the items in a <a class="localLink" href="/MediaReviewItem">MediaReviewItem</a> may often become inaccessible, but be archived by archival, journalistic, activist, or law enforcement organizations. In such cases, the referenced page may not directly publish the content.');
t('Indicates a page or other link involved in archival of a <a class="localLink" href="/CreativeWork">CreativeWork</a>.');

// https://schema.org/archiveHeld
t("archiveHeld");
t('Collection, <a href="https://en.wikipedia.org/wiki/Fonds">fonds</a>, or item held, kept or maintained by an <a class="localLink" href="/ArchiveOrganization">ArchiveOrganization</a>.');

// https://schema.org/area
t("area");
t("The area within which users can expect to reach the broadcast service.");

// https://schema.org/areaServed
t("areaServed");
t("The geographic area where a service or offered item is provided.");

// https://schema.org/arrivalAirport
t("arrivalAirport");
t("The airport where the flight terminates.");

// https://schema.org/arrivalBoatTerminal
t("arrivalBoatTerminal");
t("The terminal or port from which the boat arrives.");

// https://schema.org/arrivalBusStop
t("arrivalBusStop");
t("The stop or station from which the bus arrives.");

// https://schema.org/arrivalGate
t("arrivalGate");
t("Identifier of the flight's arrival gate.");

// https://schema.org/arrivalPlatform
t("arrivalPlatform");
t("The platform where the train arrives.");

// https://schema.org/arrivalStation
t("arrivalStation");
t("The station where the train trip ends.");

// https://schema.org/arrivalTerminal
t("arrivalTerminal");
t("Identifier of the flight's arrival terminal.");

// https://schema.org/arrivalTime
t("arrivalTime");
t("The expected arrival time.");

// https://schema.org/artEdition
t("artEdition");
t('The number of copies when multiple copies of a piece of artwork are produced - e.g. for a limited edition of 20 prints, \'artEdition\' refers to the total number of copies (in this example "20").');

// https://schema.org/arterialBranch
t("arterialBranch");
t("The branches that comprise the arterial structure.");

// https://schema.org/artform
t("artform");
t("e.g. Painting, Drawing, Sculpture, Print, Photograph, Assemblage, Collage, etc.");

// https://schema.org/articleBody
t("articleBody");
t("The actual body of the article.");

// https://schema.org/articleSection
t("articleSection");
t("Articles may belong to one or more 'sections' in a magazine or newspaper, such as Sports, Lifestyle, etc.");

// https://schema.org/artist
t("artist");
t("The primary artist for a work
        in a medium other than pencils or digital line art--for example, if the
        primary artwork is done in watercolors or digital paints.");

// https://schema.org/artMedium
t("artMedium");
t("The material used. (E.g. Oil, Watercolour, Acrylic, Linoprint, Marble, Cyanotype, Digital, Lithograph, DryPoint, Intaglio, Pastel, Woodcut, Pencil, Mixed Media, etc.)");
t("The material used.");

// https://schema.org/artworkSurface
t("artworkSurface");
t("The supporting materials for the artwork, e.g. Canvas, Paper, Wood, Board, etc.");

// https://schema.org/asin
t("asin");
t('An Amazon Standard Identification Number (ASIN) is a 10-character alphanumeric unique identifier assigned by Amazon.com and its partners for product identification within the Amazon organization (summary from <a href="https://en.wikipedia.org/wiki/Amazon_Standard_Identification_Number">Wikipedia</a>\'s article).<br/><br/>

Note also that this is a definition for how to include ASINs in Schema.org data, and not a definition of ASINs in general - see documentation from Amazon for authoritative details.
ASINs are most commonly encoded as text strings, but the [asin] property supports URL/URI as potential values too.');
t('An Amazon Standard Identification Number (ASIN) is a 10-character alphanumeric unique identifier assigned by Amazon.com and its partners for product identification within the Amazon organization (summary from <a href="https://en.wikipedia.org/wiki/Amazon_Standard_Identification_Number">Wikipedia</a>\'s article).');

// https://schema.org/aspect
t("aspect");
t("An aspect of medical practice that is considered on the page, such as 'diagnosis', 'treatment', 'causes', 'prognosis', 'etiology', 'epidemiology', etc.");

// https://schema.org/assembly
t("assembly");
t("Library file name, e.g., mscorlib.dll, system.web.dll.");

// https://schema.org/assemblyVersion
t("assemblyVersion");
t("Associated product/technology version. E.g., .NET Framework 4.5.");
t("Associated product/technology version.");

// https://schema.org/assesses
t("assesses");
t("The item being described is intended to assess the competency or learning outcome defined by the referenced term.");

// https://schema.org/associatedAnatomy
t("associatedAnatomy");
t("The anatomy of the underlying organ system or structures associated with this entity.");

// https://schema.org/associatedArticle
t("associatedArticle");
t("A NewsArticle associated with the Media Object.");

// https://schema.org/associatedClaimReview
t("associatedClaimReview");
t('An associated <a class="localLink" href="/ClaimReview">ClaimReview</a>, related by specific common content, topic or claim. The expectation is that this property would be most typically used in cases where a single activity is conducting both claim reviews and media reviews, in which case <a class="localLink" href="/relatedMediaReview">relatedMediaReview</a> would commonly be used on a <a class="localLink" href="/ClaimReview">ClaimReview</a>, while <a class="localLink" href="/relatedClaimReview">relatedClaimReview</a> would be used on <a class="localLink" href="/MediaReview">MediaReview</a>.');
t('An associated <a class="localLink" href="/ClaimReview">ClaimReview</a>, related by specific common content, topic or claim.');

// https://schema.org/associatedDisease
t("associatedDisease");
t("Disease associated to this BioChemEntity. Such disease can be a MedicalCondition or a URL. If you want to add an evidence supporting the association, please use PropertyValue.");
t("Disease associated to this BioChemEntity.");

// https://schema.org/associatedMedia
t("associatedMedia");
t("A media object that encodes this CreativeWork. This property is a synonym for encoding.");
t("A media object that encodes this CreativeWork.");

// https://schema.org/associatedMediaReview
t("associatedMediaReview");
t('An associated <a class="localLink" href="/MediaReview">MediaReview</a>, related by specific common content, topic or claim. The expectation is that this property would be most typically used in cases where a single activity is conducting both claim reviews and media reviews, in which case <a class="localLink" href="/relatedMediaReview">relatedMediaReview</a> would commonly be used on a <a class="localLink" href="/ClaimReview">ClaimReview</a>, while <a class="localLink" href="/relatedClaimReview">relatedClaimReview</a> would be used on <a class="localLink" href="/MediaReview">MediaReview</a>.');
t('An associated <a class="localLink" href="/MediaReview">MediaReview</a>, related by specific common content, topic or claim.');

// https://schema.org/associatedPathophysiology
t("associatedPathophysiology");
t("If applicable, a description of the pathophysiology associated with the anatomical system, including potential abnormal changes in the mechanical, physical, and biochemical functions of the system.");

// https://schema.org/associatedReview
t("associatedReview");
t('An associated <a class="localLink" href="/Review">Review</a>.');

// https://schema.org/athlete
t("athlete");
t("A person that acts as performing member of a sports team; a player as opposed to a coach.");

// https://schema.org/attendee
t("attendee");
t("A person or organization attending the event.");

// https://schema.org/attendees
t("attendees");
t("A person attending the event.");

// https://schema.org/audience
t("audience");
t("An intended audience, i.e. a group for whom something was created.");

// https://schema.org/audienceType
t("audienceType");
t("The target group associated with a given audience (e.g. veterans, car owners, musicians, etc.).");

// https://schema.org/audio
t("audio");
t("An embedded audio object.");

// https://schema.org/auditDate
t("auditDate");
t('Date when a certification was last audited. See also  <a href="https://www.gs1.org/voc/certificationAuditDate">gs1:certificationAuditDate</a>.');
t("Date when a certification was last audited.");

// https://schema.org/authenticator
t("authenticator");
t("The Organization responsible for authenticating the user's subscription. For example, many media apps require a cable/satellite provider to authenticate your subscription before playing media.");
t("The Organization responsible for authenticating the user's subscription.");

// https://schema.org/author
t("author");
t("The author of this content or rating. Please note that author is special in that HTML 5 provides a special mechanism for indicating authorship via the rel tag. That is equivalent to this and may be used interchangeably.");
t("The author of this content or rating.");

// https://schema.org/availability
t("availability");
t("The availability of this item&#x2014;for example In stock, Out of stock, Pre-order, etc.");

// https://schema.org/availabilityEnds
t("availabilityEnds");
t("The end of the availability of the product or service included in the offer.");

// https://schema.org/availabilityStarts
t("availabilityStarts");
t("The beginning of the availability of the product or service included in the offer.");

// https://schema.org/availableAtOrFrom
t("availableAtOrFrom");
t("The place(s) from which the offer can be obtained (e.g. store locations).");

// https://schema.org/availableChannel
t("availableChannel");
t("A means of accessing the service (e.g. a phone bank, a web site, a location, etc.).");

// https://schema.org/availableDeliveryMethod
t("availableDeliveryMethod");
t("The delivery method(s) available for this offer.");

// https://schema.org/availableFrom
t("availableFrom");
t("When the item is available for pickup from the store, locker, etc.");

// https://schema.org/availableIn
t("availableIn");
t("The location in which the strength is available.");

// https://schema.org/availableLanguage
t("availableLanguage");
t('A language someone may use with or at the item, service or place. Please use one of the language codes from the <a href="http://tools.ietf.org/html/bcp47">IETF BCP 47 standard</a>. See also <a class="localLink" href="/inLanguage">inLanguage</a>.');
t("A language someone may use with or at the item, service or place.");

// https://schema.org/availableOnDevice
t("availableOnDevice");
t("Device required to run the application. Used in cases where a specific make/model is required to run the application.");
t("Device required to run the application.");

// https://schema.org/availableService
t("availableService");
t("A medical service available from this provider.");

// https://schema.org/availableStrength
t("availableStrength");
t("An available dosage strength for the drug.");

// https://schema.org/availableTest
t("availableTest");
t("A diagnostic test or procedure offered by this lab.");

// https://schema.org/availableThrough
t("availableThrough");
t("After this date, the item will no longer be available for pickup.");

// https://schema.org/award
t("award");
t("An award won by or for this item.");

// https://schema.org/awards
t("awards");
t("Awards won by or for this item.");

// https://schema.org/awayTeam
t("awayTeam");
t("The away team in a sports event.");

// https://schema.org/backstory
t("backstory");
t('For an <a class="localLink" href="/Article">Article</a>, typically a <a class="localLink" href="/NewsArticle">NewsArticle</a>, the backstory property provides a textual summary giving a brief explanation of why and how an article was created. In a journalistic setting this could include information about reporting process, methods, interviews, data sources, etc.');
t('For an <a class="localLink" href="/Article">Article</a>, typically a <a class="localLink" href="/NewsArticle">NewsArticle</a>, the backstory property provides a textual summary giving a brief explanation of why and how an article was created.');

// https://schema.org/bankAccountType
t("bankAccountType");
t("The type of a bank account.");

// https://schema.org/baseSalary
t("baseSalary");
t("The base salary of the job or of an employee in an EmployeeRole.");

// https://schema.org/bccRecipient
t("bccRecipient");
t("A sub property of recipient. The recipient blind copied on a message.");
t("A sub property of recipient.");

// https://schema.org/bed
t("bed");
t("The type of bed or beds included in the accommodation. For the single case of just one bed of a certain type, you use bed directly with a text.
      If you want to indicate the quantity of a certain kind of bed, use an instance of BedDetails. For more detailed information, use the amenityFeature property.");
t("The type of bed or beds included in the accommodation.");

// https://schema.org/beforeMedia
t("beforeMedia");
t("A media object representing the circumstances before performing this direction.");

// https://schema.org/beneficiaryBank
t("beneficiaryBank");
t("A bank or bank’s branch, financial institution or international financial institution operating the beneficiary’s bank account or releasing funds for the beneficiary.");

// https://schema.org/benefits
t("benefits");
t("Description of benefits associated with the job.");

// https://schema.org/benefitsSummaryUrl
t("benefitsSummaryUrl");
t("The URL that goes directly to the summary of benefits and coverage for the specific standard plan or plan variation.");

// https://schema.org/bestRating
t("bestRating");
t("The highest value allowed in this rating system. If bestRating is omitted, 5 is assumed.");
t("The highest value allowed in this rating system.");

// https://schema.org/billingAddress
t("billingAddress");
t("The billing address for the order.");

// https://schema.org/billingDuration
t("billingDuration");
t("Specifies for how long this price (or price component) will be billed. Can be used, for example, to model the contractual duration of a subscription or payment plan. Type can be either a Duration or a Number (in which case the unit of measurement, for example month, is specified by the unitCode property).");
t("Specifies for how long this price (or price component) will be billed.");

// https://schema.org/billingIncrement
t("billingIncrement");
t("This property specifies the minimal quantity and rounding increment that will be the basis for the billing. The unit of measurement is specified by the unitCode property.");
t("This property specifies the minimal quantity and rounding increment that will be the basis for the billing.");

// https://schema.org/billingPeriod
t("billingPeriod");
t("The time interval used to compute the invoice.");

// https://schema.org/billingStart
t("billingStart");
t("Specifies after how much time this price (or price component) becomes valid and billing starts. Can be used, for example, to model a price increase after the first year of a subscription. The unit of measurement is specified by the unitCode property.");
t("Specifies after how much time this price (or price component) becomes valid and billing starts.");

// https://schema.org/bioChemInteraction
t("bioChemInteraction");
t("A BioChemEntity that is known to interact with this item.");

// https://schema.org/bioChemSimilarity
t("bioChemSimilarity");
t("A similar BioChemEntity, e.g., obtained by fingerprint similarity algorithms.");

// https://schema.org/biologicalRole
t("biologicalRole");
t("A role played by the BioChemEntity within a biological context.");

// https://schema.org/biomechnicalClass
t("biomechnicalClass");
t("The biomechanical properties of the bone.");

// https://schema.org/birthDate
t("birthDate");
t("Date of birth.");

// https://schema.org/birthPlace
t("birthPlace");
t("The place where the person was born.");

// https://schema.org/bitrate
t("bitrate");
t("The bitrate of the media object.");

// https://schema.org/blogPost
t("blogPost");
t("A posting that is part of this blog.");

// https://schema.org/blogPosts
t("blogPosts");
t('Indicates a post that is part of a <a class="localLink" href="/Blog">Blog</a>. Note that historically, what we term a "Blog" was once known as a "weblog", and that what we term a "BlogPosting" is now often colloquially referred to as a "blog".');
t('Indicates a post that is part of a <a class="localLink" href="/Blog">Blog</a>.');

// https://schema.org/bloodSupply
t("bloodSupply");
t("The blood vessel that carries blood from the heart to the muscle.");

// https://schema.org/boardingGroup
t("boardingGroup");
t("The airline-specific indicator of boarding order / preference.");

// https://schema.org/boardingPolicy
t("boardingPolicy");
t("The type of boarding policy used by the airline (e.g. zone-based or group-based).");

// https://schema.org/bodyLocation
t("bodyLocation");
t("Location in the body of the anatomical structure.");

// https://schema.org/bodyType
t("bodyType");
t("Indicates the design and body style of the vehicle (e.g. station wagon, hatchback, etc.).");

// https://schema.org/bookEdition
t("bookEdition");
t("The edition of the book.");

// https://schema.org/bookFormat
t("bookFormat");
t("The format of the book.");

// https://schema.org/bookingAgent
t("bookingAgent");
t("'bookingAgent' is an out-dated term indicating a 'broker' that serves as a booking agent.");

// https://schema.org/bookingTime
t("bookingTime");
t("The date and time the reservation was booked.");

// https://schema.org/borrower
t("borrower");
t("A sub property of participant. The person that borrows the object being lent.");
t("A sub property of participant.");

// https://schema.org/box
t("box");
t("A box is the area enclosed by the rectangle formed by two points. The first point is the lower corner, the second point is the upper corner. A box is expressed as two points separated by a space character.");
t("A box is the area enclosed by the rectangle formed by two points.");

// https://schema.org/branch
t("branch");
t('The branches that delineate from the nerve bundle. Not to be confused with <a class="localLink" href="/branchOf">branchOf</a>.');
t("The branches that delineate from the nerve bundle.");

// https://schema.org/branchCode
t("branchCode");
t('A short textual code (also called "store code") that uniquely identifies a place of business. The code is typically assigned by the parentOrganization and used in structured URLs.<br/><br/>

For example, in the URL http://www.starbucks.co.uk/store-locator/etc/detail/3047 the code "3047" is a branchCode for a particular branch.');
t('A short textual code (also called "store code") that uniquely identifies a place of business.');

// https://schema.org/branchOf
t("branchOf");
t('The larger organization that this local business is a branch of, if any. Not to be confused with (anatomical) <a class="localLink" href="/branch">branch</a>.');
t("The larger organization that this local business is a branch of, if any.");

// https://schema.org/brand
t("brand");
t("The brand(s) associated with a product or service, or the brand(s) maintained by an organization or business person.");

// https://schema.org/breadcrumb
t("breadcrumb");
t("A set of links that can help a user understand and navigate a website hierarchy.");

// https://schema.org/breastfeedingWarning
t("breastfeedingWarning");
t("Any precaution, guidance, contraindication, etc. related to this drug's use by breastfeeding mothers.");

// https://schema.org/broadcastAffiliateOf
t("broadcastAffiliateOf");
t("The media network(s) whose content is broadcast on this station.");

// https://schema.org/broadcastChannelId
t("broadcastChannelId");
t("The unique address by which the BroadcastService can be identified in a provider lineup. In US, this is typically a number.");
t("The unique address by which the BroadcastService can be identified in a provider lineup.");

// https://schema.org/broadcastDisplayName
t("broadcastDisplayName");
t("The name displayed in the channel guide. For many US affiliates, it is the network name.");
t("The name displayed in the channel guide.");

// https://schema.org/broadcaster
t("broadcaster");
t("The organization owning or operating the broadcast service.");

// https://schema.org/broadcastFrequency
t("broadcastFrequency");
t('The frequency used for over-the-air broadcasts. Numeric values or simple ranges, e.g. 87-99. In addition a shortcut idiom is supported for frequences of AM and FM radio channels, e.g. "87 FM".');
t("The frequency used for over-the-air broadcasts.");

// https://schema.org/broadcastFrequencyValue
t("broadcastFrequencyValue");
t("The frequency in MHz for a particular broadcast.");

// https://schema.org/broadcastOfEvent
t("broadcastOfEvent");
t("The event being broadcast such as a sporting event or awards ceremony.");

// https://schema.org/broadcastServiceTier
t("broadcastServiceTier");
t("The type of service required to have access to the channel (e.g. Standard or Premium).");

// https://schema.org/broadcastSignalModulation
t("broadcastSignalModulation");
t("The modulation (e.g. FM, AM, etc) used by a particular broadcast service.");

// https://schema.org/broadcastSubChannel
t("broadcastSubChannel");
t("The subchannel used for the broadcast.");

// https://schema.org/broadcastTimezone
t("broadcastTimezone");
t('The timezone in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 format</a> for which the service bases its broadcasts.');

// https://schema.org/broker
t("broker");
t("An entity that arranges for an exchange between a buyer and a seller.  In most cases a broker never acquires or releases ownership of a product or service involved in an exchange.  If it is not clear whether an entity is a broker, seller, or buyer, the latter two terms are preferred.");
t("An entity that arranges for an exchange between a buyer and a seller.");

// https://schema.org/browserRequirements
t("browserRequirements");
t("Specifies browser requirements in human-readable text. For example, 'requires HTML5 support'.");
t("Specifies browser requirements in human-readable text.");

// https://schema.org/businessDays
t("businessDays");
t("Days of the week when the merchant typically operates, indicated via opening hours markup.");

// https://schema.org/businessFunction
t("businessFunction");
t("The business function (e.g. sell, lease, repair, dispose) of the offer or component of a bundle (TypeAndQuantityNode). The default is http://purl.org/goodrelations/v1#Sell.");
t("The business function (e.g. sell, lease, repair, dispose) of the offer or component of a bundle (TypeAndQuantityNode).");

// https://schema.org/busName
t("busName");
t("The name of the bus (e.g. Bolt Express).");

// https://schema.org/busNumber
t("busNumber");
t("The unique identifier for the bus.");

// https://schema.org/buyer
t("buyer");
t("A sub property of participant. The participant/person/organization that bought the object.");
t("A sub property of participant.");

// https://schema.org/byArtist
t("byArtist");
t("The artist that performed this album or recording.");

// https://schema.org/byDay
t("byDay");
t('Defines the day(s) of the week on which a recurring <a class="localLink" href="/Event">Event</a> takes place. May be specified using either <a class="localLink" href="/DayOfWeek">DayOfWeek</a>, or alternatively <a class="localLink" href="/Text">Text</a> conforming to iCal\'s syntax for byDay recurrence rules.');
t('Defines the day(s) of the week on which a recurring <a class="localLink" href="/Event">Event</a> takes place.');

// https://schema.org/byMonth
t("byMonth");
t('Defines the month(s) of the year on which a recurring <a class="localLink" href="/Event">Event</a> takes place. Specified as an <a class="localLink" href="/Integer">Integer</a> between 1-12. January is 1.');
t('Defines the month(s) of the year on which a recurring <a class="localLink" href="/Event">Event</a> takes place.');

// https://schema.org/byMonthDay
t("byMonthDay");
t('Defines the day(s) of the month on which a recurring <a class="localLink" href="/Event">Event</a> takes place. Specified as an <a class="localLink" href="/Integer">Integer</a> between 1-31.');
t('Defines the day(s) of the month on which a recurring <a class="localLink" href="/Event">Event</a> takes place.');

// https://schema.org/byMonthWeek
t("byMonthWeek");
t("Defines the week(s) of the month on which a recurring Event takes place. Specified as an Integer between 1-5. For clarity, byMonthWeek is best used in conjunction with byDay to indicate concepts like the first and third Mondays of a month.");
t("Defines the week(s) of the month on which a recurring Event takes place.");

// https://schema.org/callSign
t("callSign");
t('A <a href="https://en.wikipedia.org/wiki/Call_sign">callsign</a>, as used in broadcasting and radio communications to identify people, radio and TV stations, or vehicles.');

// https://schema.org/calories
t("calories");
t("The number of calories.");

// https://schema.org/candidate
t("candidate");
t("A sub property of object. The candidate subject of this action.");
t("A sub property of object.");

// https://schema.org/caption
t("caption");
t('The caption for this object. For downloadable machine formats (closed caption, subtitles etc.) use MediaObject and indicate the <a class="localLink" href="/encodingFormat">encodingFormat</a>.');
t("The caption for this object.");

// https://schema.org/carbohydrateContent
t("carbohydrateContent");
t("The number of grams of carbohydrates.");

// https://schema.org/cargoVolume
t("cargoVolume");
t('The available volume for cargo or luggage. For automobiles, this is usually the trunk volume.<br/><br/>

Typical unit code(s): LTR for liters, FTQ for cubic foot/feet<br/><br/>

Note: You can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.');
t("The available volume for cargo or luggage.");

// https://schema.org/carrier
t("carrier");
t("'carrier' is an out-dated term indicating the 'provider' for parcel delivery and flights.");

// https://schema.org/carrierRequirements
t("carrierRequirements");
t("Specifies specific carrier(s) requirements for the application (e.g. an application may only work on a specific carrier network).");

// https://schema.org/cashBack
t("cashBack");
t("A cardholder benefit that pays the cardholder a small percentage of their net expenditures.");

// https://schema.org/catalog
t("catalog");
t("A data catalog which contains this dataset.");

// https://schema.org/catalogNumber
t("catalogNumber");
t("The catalog number for the release.");

// https://schema.org/category
t("category");
t("A category for the item. Greater signs or slashes can be used to informally indicate a category hierarchy.");
t("A category for the item.");

// https://schema.org/causeOf
t("causeOf");
t("The condition, complication, symptom, sign, etc. caused.");

// https://schema.org/ccRecipient
t("ccRecipient");
t("A sub property of recipient. The recipient copied on a message.");
t("A sub property of recipient.");

// https://schema.org/certificationIdentification
t("certificationIdentification");
t('Identifier of a certification instance (as registered with an independent certification body). Typically this identifier can be used to consult and verify the certification instance. See also <a href="https://www.gs1.org/voc/certificationIdentification">gs1:certificationIdentification</a>.');
t("Identifier of a certification instance (as registered with an independent certification body).");

// https://schema.org/certificationStatus
t("certificationStatus");
t('Indicates the current status of a certification: active or inactive. See also  <a href="https://www.gs1.org/voc/certificationStatus">gs1:certificationStatus</a>.');
t("Indicates the current status of a certification: active or inactive.");

// https://schema.org/character
t("character");
t("Fictional person connected with a creative work.");

// https://schema.org/characterAttribute
t("characterAttribute");
t("A piece of data that represents a particular aspect of a fictional character (skill, power, character points, advantage, disadvantage).");

// https://schema.org/characterName
t("characterName");
t("The name of a character played in some acting or performing role, i.e. in a PerformanceRole.");

// https://schema.org/cheatCode
t("cheatCode");
t("Cheat codes to the game.");

// https://schema.org/checkinTime
t("checkinTime");
t("The earliest someone may check into a lodging establishment.");

// https://schema.org/checkoutPageURLTemplate
t("checkoutPageURLTemplate");
t("A URL template (RFC 6570) for a checkout page for an offer. This approach allows merchants to specify a URL for online checkout of the offered product, by interpolating parameters such as the logged in user ID, product ID, quantity, discount code etc. Parameter naming and standardization are not specified here.");
t("A URL template (RFC 6570) for a checkout page for an offer.");

// https://schema.org/checkoutTime
t("checkoutTime");
t("The latest someone may check out of a lodging establishment.");

// https://schema.org/chemicalComposition
t("chemicalComposition");
t("The chemical composition describes the identity and relative ratio of the chemical elements that make up the substance.");

// https://schema.org/chemicalRole
t("chemicalRole");
t("A role played by the BioChemEntity within a chemical context.");

// https://schema.org/childMaxAge
t("childMaxAge");
t("Maximal age of the child.");

// https://schema.org/childMinAge
t("childMinAge");
t("Minimal age of the child.");

// https://schema.org/children
t("children");
t("A child of the person.");

// https://schema.org/childTaxon
t("childTaxon");
t("Closest child taxa of the taxon in question.");

// https://schema.org/cholesterolContent
t("cholesterolContent");
t("The number of milligrams of cholesterol.");

// https://schema.org/circle
t("circle");
t("A circle is the circular region of a specified radius centered at a specified latitude and longitude. A circle is expressed as a pair followed by a radius in meters.");
t("A circle is the circular region of a specified radius centered at a specified latitude and longitude.");

// https://schema.org/citation
t("citation");
t("A citation or reference to another creative work, such as another publication, web page, scholarly article, etc.");

// https://schema.org/claimInterpreter
t("claimInterpreter");
t('For a <a class="localLink" href="/Claim">Claim</a> interpreted from <a class="localLink" href="/MediaObject">MediaObject</a> content
    sed to indicate a claim contained, implied or refined from the content of a <a class="localLink" href="/MediaObject">MediaObject</a>.');

// https://schema.org/claimReviewed
t("claimReviewed");
t("A short summary of the specific claims reviewed in a ClaimReview.");

// https://schema.org/clincalPharmacology
t("clincalPharmacology");
t("Description of the absorption and elimination of drugs, including their concentration (pharmacokinetics, pK) and biological effects (pharmacodynamics, pD).");

// https://schema.org/clinicalPharmacology
t("clinicalPharmacology");
t("Description of the absorption and elimination of drugs, including their concentration (pharmacokinetics, pK) and biological effects (pharmacodynamics, pD).");

// https://schema.org/clipNumber
t("clipNumber");
t("Position of the clip within an ordered group of clips.");

// https://schema.org/closes
t("closes");
t("The closing hour of the place or service on the given day(s) of the week.");

// https://schema.org/coach
t("coach");
t("A person that acts in a coaching role for a sports team.");

// https://schema.org/code
t("code");
t("A medical code for the entity, taken from a controlled vocabulary or ontology such as ICD-9, DiseasesDB, MeSH, SNOMED-CT, RxNorm, etc.");

// https://schema.org/codeRepository
t("codeRepository");
t("Link to the repository where the un-compiled, human readable code and related code is located (SVN, GitHub, CodePlex).");

// https://schema.org/codeSampleType
t("codeSampleType");
t("What type of code sample: full (compile ready) solution, code snippet, inline code, scripts, template.");

// https://schema.org/codeValue
t("codeValue");
t("A short textual code that uniquely identifies the value.");

// https://schema.org/codingSystem
t("codingSystem");
t("The coding system, e.g. 'ICD-10'.");

// https://schema.org/colleague
t("colleague");
t("A colleague of the person.");

// https://schema.org/colleagues
t("colleagues");
t("A colleague of the person.");

// https://schema.org/collection
t("collection");
t("A sub property of object. The collection target of the action.");
t("A sub property of object.");

// https://schema.org/collectionSize
t("collectionSize");
t('The number of items in the <a class="localLink" href="/Collection">Collection</a>.');

// https://schema.org/color
t("color");
t("The color of the product.");

// https://schema.org/colorist
t("colorist");
t("The individual who adds color to inked drawings.");

// https://schema.org/colorSwatch
t("colorSwatch");
t('A color swatch image, visualizing the color of a <a class="localLink" href="/Product">Product</a>. Should match the textual description specified in the <a class="localLink" href="/color">color</a> property. This can be a URL or a fully described ImageObject.');
t('A color swatch image, visualizing the color of a <a class="localLink" href="/Product">Product</a>.');

// https://schema.org/comment
t("comment");
t("Comments, typically from users.");

// https://schema.org/commentCount
t("commentCount");
t("The number of comments this CreativeWork (e.g. Article, Question or Answer) has received. This is most applicable to works published in Web sites with commenting system; additional comments may exist elsewhere.");
t("The number of comments this CreativeWork (e.g. Article, Question or Answer) has received.");

// https://schema.org/commentText
t("commentText");
t("The text of the UserComment.");

// https://schema.org/commentTime
t("commentTime");
t("The time at which the UserComment was made.");

// https://schema.org/competencyRequired
t("competencyRequired");
t("Knowledge, skill, ability or personal attribute that must be demonstrated by a person or other entity in order to do something such as earn an Educational Occupational Credential or understand a LearningResource.");

// https://schema.org/competitor
t("competitor");
t("A competitor in a sports event.");

// https://schema.org/composer
t("composer");
t("The person or organization who wrote a composition, or who is the composer of a work performed at some event.");

// https://schema.org/comprisedOf
t("comprisedOf");
t("Specifying something physically contained by something else. Typically used here for the underlying anatomical structures, such as organs, that comprise the anatomical system.");
t("Specifying something physically contained by something else.");

// https://schema.org/conditionsOfAccess
t("conditionsOfAccess");
t('Conditions that affect the availability of, or method(s) of access to, an item. Typically used for real world items such as an <a class="localLink" href="/ArchiveComponent">ArchiveComponent</a> held by an <a class="localLink" href="/ArchiveOrganization">ArchiveOrganization</a>. This property is not suitable for use as a general Web access control mechanism. It is expressed only in natural language.<br/><br/>

For example "Available by appointment from the Reading Room" or "Accessible only from logged-in accounts ".');
t("Conditions that affect the availability of, or method(s) of access to, an item.");

// https://schema.org/confirmationNumber
t("confirmationNumber");
t("A number that confirms the given order or payment has been received.");

// https://schema.org/connectedTo
t("connectedTo");
t("Other anatomical structures to which this structure is connected.");

// https://schema.org/constraintProperty
t("constraintProperty");
t('Indicates a property used as a constraint. For example, in the definition of a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>. The value is a property, either from within Schema.org or from other compatible (e.g. RDF) systems such as DataCommons.org or Wikidata.org.');
t("Indicates a property used as a constraint.");

// https://schema.org/contactlessPayment
t("contactlessPayment");
t("A secure method for consumers to purchase products or services via debit, credit or smartcards by using RFID or NFC technology.");

// https://schema.org/contactOption
t("contactOption");
t("An option available on this contact point (e.g. a toll-free number or support for hearing-impaired callers).");

// https://schema.org/contactPoint
t("contactPoint");
t("A contact point for a person or organization.");

// https://schema.org/contactPoints
t("contactPoints");
t("A contact point for a person or organization.");

// https://schema.org/contactType
t("contactType");
t("A person or organization can have different contact points, for different purposes. For example, a sales contact point, a PR contact point and so on. This property is used to specify the kind of contact point.");
t("A person or organization can have different contact points, for different purposes.");

// https://schema.org/containedIn
t("containedIn");
t("The basic containment relation between a place and one that contains it.");

// https://schema.org/containedInPlace
t("containedInPlace");
t("The basic containment relation between a place and one that contains it.");

// https://schema.org/containsPlace
t("containsPlace");
t("The basic containment relation between a place and another that it contains.");

// https://schema.org/containsSeason
t("containsSeason");
t("A season that is part of the media series.");

// https://schema.org/contentLocation
t("contentLocation");
t("The location depicted or described in the content. For example, the location in a photograph or painting.");
t("The location depicted or described in the content.");

// https://schema.org/contentRating
t("contentRating");
t("Official rating of a piece of content&#x2014;for example, 'MPAA PG-13'.");

// https://schema.org/contentReferenceTime
t("contentReferenceTime");
t("The specific time described by a creative work, for works (e.g. articles, video objects etc.) that emphasise a particular moment within an Event.");

// https://schema.org/contentSize
t("contentSize");
t("File size in (mega/kilo)bytes.");

// https://schema.org/contentType
t("contentType");
t("The supported content type(s) for an EntryPoint response.");

// https://schema.org/contentUrl
t("contentUrl");
t("Actual bytes of the media object, for example the image file or video file.");

// https://schema.org/contraindication
t("contraindication");
t("A contraindication for this therapy.");

// https://schema.org/contributor
t("contributor");
t("A secondary contributor to the CreativeWork or Event.");

// https://schema.org/cookingMethod
t("cookingMethod");
t("The method of cooking, such as Frying, Steaming, ...");

// https://schema.org/cookTime
t("cookTime");
t('The time it takes to actually cook the dish, in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 duration format</a>.');

// https://schema.org/copyrightHolder
t("copyrightHolder");
t("The party holding the legal copyright to the CreativeWork.");

// https://schema.org/copyrightNotice
t("copyrightNotice");
t("Text of a notice appropriate for describing the copyright aspects of this Creative Work, ideally indicating the owner of the copyright for the Work.");

// https://schema.org/copyrightYear
t("copyrightYear");
t("The year during which the claimed copyright for the CreativeWork was first asserted.");

// https://schema.org/correction
t("correction");
t('Indicates a correction to a <a class="localLink" href="/CreativeWork">CreativeWork</a>, either via a <a class="localLink" href="/CorrectionComment">CorrectionComment</a>, textually or in another document.');

// https://schema.org/correctionsPolicy
t("correctionsPolicy");
t('For an <a class="localLink" href="/Organization">Organization</a> (e.g. <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>), a statement describing (in news media, the newsroom’s) disclosure and correction policy for errors.');

// https://schema.org/costCategory
t("costCategory");
t("The category of cost, such as wholesale, retail, reimbursement cap, etc.");

// https://schema.org/costCurrency
t("costCurrency");
t("The currency (in 3-letter) of the drug cost. See: http://en.wikipedia.org/wiki/ISO_4217.");
t("The currency (in 3-letter) of the drug cost.");

// https://schema.org/costOrigin
t("costOrigin");
t("Additional details to capture the origin of the cost data. For example, 'Medicare Part B'.");
t("Additional details to capture the origin of the cost data.");

// https://schema.org/costPerUnit
t("costPerUnit");
t("The cost per unit of the drug.");

// https://schema.org/countriesNotSupported
t("countriesNotSupported");
t("Countries for which the application is not supported. You can also provide the two-letter ISO 3166-1 alpha-2 country code.");
t("Countries for which the application is not supported.");

// https://schema.org/countriesSupported
t("countriesSupported");
t("Countries for which the application is supported. You can also provide the two-letter ISO 3166-1 alpha-2 country code.");
t("Countries for which the application is supported.");

// https://schema.org/countryOfAssembly
t("countryOfAssembly");
t("The place where the product was assembled.");

// https://schema.org/countryOfLastProcessing
t("countryOfLastProcessing");
t('The place where the item (typically <a class="localLink" href="/Product">Product</a>) was last processed and tested before importation.');

// https://schema.org/countryOfOrigin
t("countryOfOrigin");
t('The country of origin of something, including products as well as creative  works such as movie and TV content.<br/><br/>

In the case of TV and movie, this would be the country of the principle offices of the production company or individual responsible for the movie. For other kinds of <a class="localLink" href="/CreativeWork">CreativeWork</a> it is difficult to provide fully general guidance, and properties such as <a class="localLink" href="/contentLocation">contentLocation</a> and <a class="localLink" href="/locationCreated">locationCreated</a> may be more applicable.<br/><br/>

In the case of products, the country of origin of the product. The exact interpretation of this may vary by context and product type, and cannot be fully enumerated here.');
t("The country of origin of something, including products as well as creative  works such as movie and TV content.");

// https://schema.org/course
t("course");
t("A sub property of location. The course where this action was taken.");
t("A sub property of location.");

// https://schema.org/courseCode
t("courseCode");
t('The identifier for the <a class="localLink" href="/Course">Course</a> used by the course <a class="localLink" href="/provider">provider</a> (e.g. CS101 or 6.001).');

// https://schema.org/courseMode
t("courseMode");
t('The medium or means of delivery of the course instance or the mode of study, either as a text label (e.g. "online", "onsite" or "blended"; "synchronous" or "asynchronous"; "full-time" or "part-time") or as a URL reference to a term from a controlled vocabulary (e.g. https://ceds.ed.gov/element/001311#Asynchronous).');

// https://schema.org/coursePrerequisites
t("coursePrerequisites");
t('Requirements for taking the Course. May be completion of another <a class="localLink" href="/Course">Course</a> or a textual description like "permission of instructor". Requirements may be a pre-requisite competency, referenced using <a class="localLink" href="/AlignmentObject">AlignmentObject</a>.');
t("Requirements for taking the Course.");

// https://schema.org/courseSchedule
t("courseSchedule");
t('Represents the length and pace of a course, expressed as a <a class="localLink" href="/Schedule">Schedule</a>.');

// https://schema.org/courseWorkload
t("courseWorkload");
t('The amount of work expected of students taking the course, often provided as a figure per week or per month, and may be broken down by type. For example, "2 hours of lectures, 1 hour of lab work and 3 hours of independent study per week".');
t("The amount of work expected of students taking the course, often provided as a figure per week or per month, and may be broken down by type.");

// https://schema.org/coverageEndTime
t("coverageEndTime");
t("The time when the live blog will stop covering the Event. Note that coverage may continue after the Event concludes.");
t("The time when the live blog will stop covering the Event.");

// https://schema.org/coverageStartTime
t("coverageStartTime");
t("The time when the live blog will begin covering the Event. Note that coverage may begin before the Event's start time. The LiveBlogPosting may also be created before coverage begins.");
t("The time when the live blog will begin covering the Event.");

// https://schema.org/creativeWorkStatus
t("creativeWorkStatus");
t("The status of a creative work in terms of its stage in a lifecycle. Example terms include Incomplete, Draft, Published, Obsolete. Some organizations define a set of terms for the stages of their publication lifecycle.");
t("The status of a creative work in terms of its stage in a lifecycle.");

// https://schema.org/creator
t("creator");
t("The creator/author of this CreativeWork. This is the same as the Author property for CreativeWork.");
t("The creator/author of this CreativeWork.");

// https://schema.org/credentialCategory
t("credentialCategory");
t('The category or type of credential being described, for example "degree”, “certificate”, “badge”, or more specific term.');

// https://schema.org/creditedTo
t("creditedTo");
t('The group the release is credited to if different than the byArtist. For example, Red and Blue is credited to "Stefani Germanotta Band", but by Lady Gaga.');
t("The group the release is credited to if different than the byArtist.");

// https://schema.org/creditText
t("creditText");
t("Text that can be used to credit person(s) and/or organization(s) associated with a published Creative Work.");

// https://schema.org/cssSelector
t("cssSelector");
t('A CSS selector, e.g. of a <a class="localLink" href="/SpeakableSpecification">SpeakableSpecification</a> or <a class="localLink" href="/WebPageElement">WebPageElement</a>. In the latter case, multiple matches within a page can constitute a single conceptual "Web page element".');
t('A CSS selector, e.g. of a <a class="localLink" href="/SpeakableSpecification">SpeakableSpecification</a> or <a class="localLink" href="/WebPageElement">WebPageElement</a>.');

// https://schema.org/currenciesAccepted
t("currenciesAccepted");
t('The currency accepted.<br/><br/>

Use standard formats: <a href="http://en.wikipedia.org/wiki/ISO_4217">ISO 4217 currency format</a>, e.g. "USD"; <a href="https://en.wikipedia.org/wiki/List_of_cryptocurrencies">Ticker symbol</a> for cryptocurrencies, e.g. "BTC"; well known names for <a href="https://en.wikipedia.org/wiki/Local_exchange_trading_system">Local Exchange Trading Systems</a> (LETS) and other currency types, e.g. "Ithaca HOUR".');
t("The currency accepted.");

// https://schema.org/currency
t("currency");
t('The currency in which the monetary amount is expressed.<br/><br/>

Use standard formats: <a href="http://en.wikipedia.org/wiki/ISO_4217">ISO 4217 currency format</a>, e.g. "USD"; <a href="https://en.wikipedia.org/wiki/List_of_cryptocurrencies">Ticker symbol</a> for cryptocurrencies, e.g. "BTC"; well known names for <a href="https://en.wikipedia.org/wiki/Local_exchange_trading_system">Local Exchange Trading Systems</a> (LETS) and other currency types, e.g. "Ithaca HOUR".');
t("The currency in which the monetary amount is expressed.");

// https://schema.org/currentExchangeRate
t("currentExchangeRate");
t("The current price of a currency.");

// https://schema.org/customer
t("customer");
t("Party placing the order or paying the invoice.");

// https://schema.org/customerRemorseReturnFees
t("customerRemorseReturnFees");
t("The type of return fees if the product is returned due to customer remorse.");

// https://schema.org/customerRemorseReturnLabelSource
t("customerRemorseReturnLabelSource");
t("The method (from an enumeration) by which the customer obtains a return shipping label for a product returned due to customer remorse.");

// https://schema.org/customerRemorseReturnShippingFeesAmount
t("customerRemorseReturnShippingFeesAmount");
t('The amount of shipping costs if a product is returned due to customer remorse. Applicable when property <a class="localLink" href="/customerRemorseReturnFees">customerRemorseReturnFees</a> equals <a class="localLink" href="/ReturnShippingFees">ReturnShippingFees</a>.');
t("The amount of shipping costs if a product is returned due to customer remorse.");

// https://schema.org/cutoffTime
t("cutoffTime");
t('Order cutoff time allows merchants to describe the time after which they will no longer process orders received on that day. For orders processed after cutoff time, one day gets added to the delivery time estimate. This property is expected to be most typically used via the <a class="localLink" href="/ShippingRateSettings">ShippingRateSettings</a> publication pattern. The time is indicated using the ISO-8601 Time format, e.g. "23:30:00-05:00" would represent 6:30 pm Eastern Standard Time (EST) which is 5 hours behind Coordinated Universal Time (UTC).');
t("Order cutoff time allows merchants to describe the time after which they will no longer process orders received on that day.");

// https://schema.org/cvdCollectionDate
t("cvdCollectionDate");
t("collectiondate - Date for which patient counts are reported.");

// https://schema.org/cvdFacilityCounty
t("cvdFacilityCounty");
t('Name of the County of the NHSN facility that this data record applies to. Use <a class="localLink" href="/cvdFacilityId">cvdFacilityId</a> to identify the facility. To provide other details, <a class="localLink" href="/healthcareReportingData">healthcareReportingData</a> can be used on a <a class="localLink" href="/Hospital">Hospital</a> entry.');
t("Name of the County of the NHSN facility that this data record applies to.");

// https://schema.org/cvdFacilityId
t("cvdFacilityId");
t('Identifier of the NHSN facility that this data record applies to. Use <a class="localLink" href="/cvdFacilityCounty">cvdFacilityCounty</a> to indicate the county. To provide other details, <a class="localLink" href="/healthcareReportingData">healthcareReportingData</a> can be used on a <a class="localLink" href="/Hospital">Hospital</a> entry.');
t("Identifier of the NHSN facility that this data record applies to.");

// https://schema.org/cvdNumBeds
t("cvdNumBeds");
t("numbeds - HOSPITAL INPATIENT BEDS: Inpatient beds, including all staffed, licensed, and overflow (surge) beds used for inpatients.");

// https://schema.org/cvdNumBedsOcc
t("cvdNumBedsOcc");
t("numbedsocc - HOSPITAL INPATIENT BED OCCUPANCY: Total number of staffed inpatient beds that are occupied.");

// https://schema.org/cvdNumC19Died
t("cvdNumC19Died");
t("numc19died - DEATHS: Patients with suspected or confirmed COVID-19 who died in the hospital, ED, or any overflow location.");

// https://schema.org/cvdNumC19HOPats
t("cvdNumC19HOPats");
t("numc19hopats - HOSPITAL ONSET: Patients hospitalized in an NHSN inpatient care location with onset of suspected or confirmed COVID-19 14 or more days after hospitalization.");

// https://schema.org/cvdNumC19HospPats
t("cvdNumC19HospPats");
t("numc19hosppats - HOSPITALIZED: Patients currently hospitalized in an inpatient care location who have suspected or confirmed COVID-19.");

// https://schema.org/cvdNumC19MechVentPats
t("cvdNumC19MechVentPats");
t("numc19mechventpats - HOSPITALIZED and VENTILATED: Patients hospitalized in an NHSN inpatient care location who have suspected or confirmed COVID-19 and are on a mechanical ventilator.");

// https://schema.org/cvdNumC19OFMechVentPats
t("cvdNumC19OFMechVentPats");
t("numc19ofmechventpats - ED/OVERFLOW and VENTILATED: Patients with suspected or confirmed COVID-19 who are in the ED or any overflow location awaiting an inpatient bed and on a mechanical ventilator.");

// https://schema.org/cvdNumC19OverflowPats
t("cvdNumC19OverflowPats");
t("numc19overflowpats - ED/OVERFLOW: Patients with suspected or confirmed COVID-19 who are in the ED or any overflow location awaiting an inpatient bed.");

// https://schema.org/cvdNumICUBeds
t("cvdNumICUBeds");
t("numicubeds - ICU BEDS: Total number of staffed inpatient intensive care unit (ICU) beds.");

// https://schema.org/cvdNumICUBedsOcc
t("cvdNumICUBedsOcc");
t("numicubedsocc - ICU BED OCCUPANCY: Total number of staffed inpatient ICU beds that are occupied.");

// https://schema.org/cvdNumTotBeds
t("cvdNumTotBeds");
t("numtotbeds - ALL HOSPITAL BEDS: Total number of all inpatient and outpatient beds, including all staffed, ICU, licensed, and overflow (surge) beds used for inpatients or outpatients.");

// https://schema.org/cvdNumVent
t("cvdNumVent");
t("numvent - MECHANICAL VENTILATORS: Total number of ventilators available.");

// https://schema.org/cvdNumVentUse
t("cvdNumVentUse");
t("numventuse - MECHANICAL VENTILATORS IN USE: Total number of ventilators in use.");

// https://schema.org/dataFeedElement
t("dataFeedElement");
t("An item within a data feed. Data feeds may have many elements.");
t("An item within a data feed.");

// https://schema.org/dataset
t("dataset");
t("A dataset contained in this catalog.");

// https://schema.org/datasetTimeInterval
t("datasetTimeInterval");
t("The range of temporal applicability of a dataset, e.g. for a 2011 census dataset, the year 2011 (in ISO 8601 time interval format).");

// https://schema.org/dateCreated
t("dateCreated");
t("The date on which the CreativeWork was created or the item was added to a DataFeed.");

// https://schema.org/dateDeleted
t("dateDeleted");
t("The datetime the item was removed from the DataFeed.");

// https://schema.org/dateIssued
t("dateIssued");
t("The date the ticket was issued.");

// https://schema.org/dateline
t("dateline");
t('A <a href="https://en.wikipedia.org/wiki/Dateline">dateline</a> is a brief piece of text included in news articles that describes where and when the story was written or filed though the date is often omitted. Sometimes only a placename is provided.<br/><br/>

Structured representations of dateline-related information can also be expressed more explicitly using <a class="localLink" href="/locationCreated">locationCreated</a> (which represents where a work was created, e.g. where a news report was written).  For location depicted or described in the content, use <a class="localLink" href="/contentLocation">contentLocation</a>.<br/><br/>

Dateline summaries are oriented more towards human readers than towards automated processing, and can vary substantially. Some examples: "BEIRUT, Lebanon, June 2.", "Paris, France", "December 19, 2017 11:43AM Reporting from Washington", "Beijing/Moscow", "QUEZON CITY, Philippines".');
t('A <a href="https://en.wikipedia.org/wiki/Dateline">dateline</a> is a brief piece of text included in news articles that describes where and when the story was written or filed though the date is often omitted.');

// https://schema.org/dateModified
t("dateModified");
t("The date on which the CreativeWork was most recently modified or when the item's entry was modified within a DataFeed.");

// https://schema.org/datePosted
t("datePosted");
t("Publication date of an online listing.");

// https://schema.org/datePublished
t("datePublished");
t('Date of first publication or broadcast. For example the date a <a class="localLink" href="/CreativeWork">CreativeWork</a> was broadcast or a <a class="localLink" href="/Certification">Certification</a> was issued.');
t("Date of first publication or broadcast.");

// https://schema.org/dateRead
t("dateRead");
t("The date/time at which the message has been read by the recipient if a single recipient exists.");

// https://schema.org/dateReceived
t("dateReceived");
t("The date/time the message was received if a single recipient exists.");

// https://schema.org/dateSent
t("dateSent");
t("The date/time at which the message was sent.");

// https://schema.org/dateVehicleFirstRegistered
t("dateVehicleFirstRegistered");
t("The date of the first registration of the vehicle with the respective public authorities.");

// https://schema.org/dayOfWeek
t("dayOfWeek");
t("The day of the week for which these opening hours are valid.");

// https://schema.org/deathDate
t("deathDate");
t("Date of death.");

// https://schema.org/deathPlace
t("deathPlace");
t("The place where the person died.");

// https://schema.org/defaultValue
t("defaultValue");
t("The default value of the input.  For properties that expect a literal, the default is a literal value, for properties that expect an object, it's an ID reference to one of the current values.");
t("The default value of the input.");

// https://schema.org/deliveryAddress
t("deliveryAddress");
t("Destination address.");

// https://schema.org/deliveryLeadTime
t("deliveryLeadTime");
t("The typical delay between the receipt of the order and the goods either leaving the warehouse or being prepared for pickup, in case the delivery method is on site pickup.");

// https://schema.org/deliveryMethod
t("deliveryMethod");
t("A sub property of instrument. The method of delivery.");
t("A sub property of instrument.");

// https://schema.org/deliveryStatus
t("deliveryStatus");
t("New entry added as the package passes through each leg of its journey (from shipment to final delivery).");

// https://schema.org/deliveryTime
t("deliveryTime");
t("The total delay between the receipt of the order and the goods reaching the final customer.");

// https://schema.org/department
t("department");
t("A relationship between an organization and a department of that organization, also described as an organization (allowing different urls, logos, opening hours). For example: a store with a pharmacy, or a bakery with a cafe.");
t("A relationship between an organization and a department of that organization, also described as an organization (allowing different urls, logos, opening hours).");

// https://schema.org/departureAirport
t("departureAirport");
t("The airport where the flight originates.");

// https://schema.org/departureBoatTerminal
t("departureBoatTerminal");
t("The terminal or port from which the boat departs.");

// https://schema.org/departureBusStop
t("departureBusStop");
t("The stop or station from which the bus departs.");

// https://schema.org/departureGate
t("departureGate");
t("Identifier of the flight's departure gate.");

// https://schema.org/departurePlatform
t("departurePlatform");
t("The platform from which the train departs.");

// https://schema.org/departureStation
t("departureStation");
t("The station from which the train departs.");

// https://schema.org/departureTerminal
t("departureTerminal");
t("Identifier of the flight's departure terminal.");

// https://schema.org/departureTime
t("departureTime");
t("The expected departure time.");

// https://schema.org/dependencies
t("dependencies");
t("Prerequisites needed to fulfill steps in article.");

// https://schema.org/depth
t("depth");
t("The depth of the item.");

// https://schema.org/description
t("description");
t("A description of the item.");

// https://schema.org/device
t("device");
t("Device required to run the application. Used in cases where a specific make/model is required to run the application.");
t("Device required to run the application.");

// https://schema.org/diagnosis
t("diagnosis");
t("One or more alternative conditions considered in the differential diagnosis process as output of a diagnosis process.");

// https://schema.org/diagram
t("diagram");
t("An image containing a diagram that illustrates the structure and/or its component substructures and/or connections with other structures.");

// https://schema.org/diet
t("diet");
t("A sub property of instrument. The diet used in this action.");
t("A sub property of instrument.");

// https://schema.org/dietFeatures
t("dietFeatures");
t("Nutritional information specific to the dietary plan. May include dietary recommendations on what foods to avoid, what foods to consume, and specific alterations/deviations from the USDA or other regulatory body's approved dietary guidelines.");
t("Nutritional information specific to the dietary plan.");

// https://schema.org/differentialDiagnosis
t("differentialDiagnosis");
t("One of a set of differential diagnoses for the condition. Specifically, a closely-related or competing diagnosis typically considered later in the cognitive process whereby this medical condition is distinguished from others most likely responsible for a similar collection of signs and symptoms to reach the most parsimonious diagnosis or diagnoses in a patient.");
t("One of a set of differential diagnoses for the condition.");

// https://schema.org/digitalSourceType
t("digitalSourceType");
t('Indicates an IPTCDigitalSourceEnumeration code indicating the nature of the digital source(s) for some <a class="localLink" href="/CreativeWork">CreativeWork</a>.');

// https://schema.org/directApply
t("directApply");
t('Indicates whether an <a class="localLink" href="/url">url</a> that is associated with a <a class="localLink" href="/JobPosting">JobPosting</a> enables direct application for the job, via the posting website. A job posting is considered to have directApply of <a class="localLink" href="/True">True</a> if an application process for the specified job can be directly initiated via the url(s) given (noting that e.g. multiple internet domains might nevertheless be involved at an implementation level). A value of <a class="localLink" href="/False">False</a> is appropriate if there is no clear path to applying directly online for the specified job, navigating directly from the JobPosting url(s) supplied.');
t('Indicates whether an <a class="localLink" href="/url">url</a> that is associated with a <a class="localLink" href="/JobPosting">JobPosting</a> enables direct application for the job, via the posting website.');

// https://schema.org/director
t("director");
t("A director of e.g. TV, radio, movie, video gaming etc. content, or of an event. Directors can be associated with individual items or with a series, episode, clip.");
t("A director of e.g. TV, radio, movie, video gaming etc. content, or of an event.");

// https://schema.org/directors
t("directors");
t("A director of e.g. TV, radio, movie, video games etc. content. Directors can be associated with individual items or with a series, episode, clip.");
t("A director of e.g. TV, radio, movie, video games etc. content.");

// https://schema.org/disambiguatingDescription
t("disambiguatingDescription");
t("A sub property of description. A short description of the item used to disambiguate from other, similar items. Information from other properties (in particular, name) may be necessary for the description to be useful for disambiguation.");
t("A sub property of description.");

// https://schema.org/discount
t("discount");
t("Any discount applied (to an Order).");

// https://schema.org/discountCode
t("discountCode");
t("Code used to redeem a discount.");

// https://schema.org/discountCurrency
t("discountCurrency");
t('The currency of the discount.<br/><br/>

Use standard formats: <a href="http://en.wikipedia.org/wiki/ISO_4217">ISO 4217 currency format</a>, e.g. "USD"; <a href="https://en.wikipedia.org/wiki/List_of_cryptocurrencies">Ticker symbol</a> for cryptocurrencies, e.g. "BTC"; well known names for <a href="https://en.wikipedia.org/wiki/Local_exchange_trading_system">Local Exchange Trading Systems</a> (LETS) and other currency types, e.g. "Ithaca HOUR".');
t("The currency of the discount.");

// https://schema.org/discusses
t("discusses");
t("Specifies the CreativeWork associated with the UserComment.");

// https://schema.org/discussionUrl
t("discussionUrl");
t("A link to the page containing the comments of the CreativeWork.");

// https://schema.org/diseasePreventionInfo
t("diseasePreventionInfo");
t("Information about disease prevention.");

// https://schema.org/diseaseSpreadStatistics
t("diseaseSpreadStatistics");
t('Statistical information about the spread of a disease, either as <a class="localLink" href="/WebContent">WebContent</a>, or
  described directly as a <a class="localLink" href="/Dataset">Dataset</a>, or the specific <a class="localLink" href="/Observation">Observation</a>s in the dataset. When a <a class="localLink" href="/WebContent">WebContent</a> URL is
  provided, the page indicated might also contain more such markup.');
t('Statistical information about the spread of a disease, either as <a class="localLink" href="/WebContent">WebContent</a>, or
  described directly as a <a class="localLink" href="/Dataset">Dataset</a>, or the specific <a class="localLink" href="/Observation">Observation</a>s in the dataset.');

// https://schema.org/dissolutionDate
t("dissolutionDate");
t("The date that this organization was dissolved.");

// https://schema.org/distance
t("distance");
t("The distance travelled, e.g. exercising or travelling.");

// https://schema.org/distinguishingSign
t("distinguishingSign");
t("One of a set of signs and symptoms that can be used to distinguish this diagnosis from others in the differential diagnosis.");

// https://schema.org/distribution
t("distribution");
t('A downloadable form of this dataset, at a specific location, in a specific format. This property can be repeated if different variations are available. There is no expectation that different downloadable distributions must contain exactly equivalent information (see also <a href="https://www.w3.org/TR/vocab-dcat-3/#Class:Distribution">DCAT</a> on this point). Different distributions might include or exclude different subsets of the entire dataset, for example.');
t("A downloadable form of this dataset, at a specific location, in a specific format.");

// https://schema.org/diversityPolicy
t("diversityPolicy");
t('Statement on diversity policy by an <a class="localLink" href="/Organization">Organization</a> e.g. a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>. For a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>, a statement describing the newsroom’s diversity policy on both staffing and sources, typically providing staffing data.');
t('Statement on diversity policy by an <a class="localLink" href="/Organization">Organization</a> e.g. a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>.');

// https://schema.org/diversityStaffingReport
t("diversityStaffingReport");
t('For an <a class="localLink" href="/Organization">Organization</a> (often but not necessarily a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>), a report on staffing diversity issues. In a news context this might be for example ASNE or RTDNA (US) reports, or self-reported.');
t('For an <a class="localLink" href="/Organization">Organization</a> (often but not necessarily a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>), a report on staffing diversity issues.');

// https://schema.org/documentation
t("documentation");
t("Further documentation describing the Web API in more detail.");

// https://schema.org/doesNotShip
t("doesNotShip");
t('Indicates when shipping to a particular <a class="localLink" href="/shippingDestination">shippingDestination</a> is not available.');

// https://schema.org/domainIncludes
t("domainIncludes");
t("Relates a property to a class that is (one of) the type(s) the property is expected to be used on.");

// https://schema.org/domiciledMortgage
t("domiciledMortgage");
t("Whether borrower is a resident of the jurisdiction where the property is located.");

// https://schema.org/doorTime
t("doorTime");
t("The time admission will commence.");

// https://schema.org/dosageForm
t("dosageForm");
t("A dosage form in which this drug/supplement is available, e.g. 'tablet', 'suspension', 'injection'.");

// https://schema.org/doseSchedule
t("doseSchedule");
t("A dosing schedule for the drug for a given population, either observed, recommended, or maximum dose based on the type used.");

// https://schema.org/doseUnit
t("doseUnit");
t("The unit of the dose, e.g. 'mg'.");

// https://schema.org/doseValue
t("doseValue");
t("The value of the dose, e.g. 500.");

// https://schema.org/downloadUrl
t("downloadUrl");
t("If the file can be downloaded, URL to download the binary.");

// https://schema.org/downPayment
t("downPayment");
t("a type of payment made in cash during the onset of the purchase of an expensive good/service. The payment typically represents only a percentage of the full purchase price.");
t("a type of payment made in cash during the onset of the purchase of an expensive good/service.");

// https://schema.org/downvoteCount
t("downvoteCount");
t("The number of downvotes this question, answer or comment has received from the community.");

// https://schema.org/drainsTo
t("drainsTo");
t("The vasculature that the vein drains into.");

// https://schema.org/driveWheelConfiguration
t("driveWheelConfiguration");
t("The drive wheel configuration, i.e. which roadwheels will receive torque from the vehicle's engine via the drivetrain.");

// https://schema.org/dropoffLocation
t("dropoffLocation");
t("Where a rental car can be dropped off.");

// https://schema.org/dropoffTime
t("dropoffTime");
t("When a rental car can be dropped off.");

// https://schema.org/drug
t("drug");
t("Specifying a drug or medicine used in a medication procedure.");

// https://schema.org/drugClass
t("drugClass");
t("The class of drug this belongs to (e.g., statins).");

// https://schema.org/drugUnit
t("drugUnit");
t("The unit in which the drug is measured, e.g. '5 mg tablet'.");

// https://schema.org/duns
t("duns");
t("The Dun &amp; Bradstreet DUNS number for identifying an organization or business person.");

// https://schema.org/duplicateTherapy
t("duplicateTherapy");
t("A therapy that duplicates or overlaps this one.");

// https://schema.org/duration
t("duration");
t('The duration of the item (movie, audio recording, event, etc.) in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 date format</a>.');

// https://schema.org/durationOfWarranty
t("durationOfWarranty");
t("The duration of the warranty promise. Common unitCode values are ANN for year, MON for months, or DAY for days.");
t("The duration of the warranty promise.");

// https://schema.org/duringMedia
t("duringMedia");
t("A media object representing the circumstances while performing this direction.");

// https://schema.org/earlyPrepaymentPenalty
t("earlyPrepaymentPenalty");
t("The amount to be paid as a penalty in the event of early payment of the loan.");

// https://schema.org/editEIDR
t("editEIDR");
t('An <a href="https://eidr.org/">EIDR</a> (Entertainment Identifier Registry) <a class="localLink" href="/identifier">identifier</a> representing a specific edit / edition for a work of film or television.<br/><br/>

For example, the motion picture known as "Ghostbusters" whose <a class="localLink" href="/titleEIDR">titleEIDR</a> is "10.5240/7EC7-228A-510A-053E-CBB8-J" has several edits, e.g. "10.5240/1F2A-E1C5-680A-14C6-E76B-I" and "10.5240/8A35-3BEE-6497-5D12-9E4F-3".<br/><br/>

Since schema.org types like <a class="localLink" href="/Movie">Movie</a> and <a class="localLink" href="/TVEpisode">TVEpisode</a> can be used for both works and their multiple expressions, it is possible to use <a class="localLink" href="/titleEIDR">titleEIDR</a> alone (for a general description), or alongside <a class="localLink" href="/editEIDR">editEIDR</a> for a more edit-specific description.');
t('An <a href="https://eidr.org/">EIDR</a> (Entertainment Identifier Registry) <a class="localLink" href="/identifier">identifier</a> representing a specific edit / edition for a work of film or television.');

// https://schema.org/editor
t("editor");
t("Specifies the Person who edited the CreativeWork.");

// https://schema.org/educationalAlignment
t("educationalAlignment");
t('An alignment to an established educational framework.<br/><br/>

This property should not be used where the nature of the alignment can be described using a simple property, for example to express that a resource <a class="localLink" href="/teaches">teaches</a> or <a class="localLink" href="/assesses">assesses</a> a competency.');
t("An alignment to an established educational framework.");

// https://schema.org/educationalCredentialAwarded
t("educationalCredentialAwarded");
t("A description of the qualification, award, certificate, diploma or other educational credential awarded as a consequence of successful completion of this course or program.");

// https://schema.org/educationalFramework
t("educationalFramework");
t("The framework to which the resource being described is aligned.");

// https://schema.org/educationalLevel
t("educationalLevel");
t("The level in terms of progression through an educational or training context. Examples of educational levels include 'beginner', 'intermediate' or 'advanced', and formal sets of level indicators.");
t("The level in terms of progression through an educational or training context.");

// https://schema.org/educationalProgramMode
t("educationalProgramMode");
t('Similar to courseMode, the medium or means of delivery of the program as a whole. The value may either be a text label (e.g. "online", "onsite" or "blended"; "synchronous" or "asynchronous"; "full-time" or "part-time") or a URL reference to a term from a controlled vocabulary (e.g. https://ceds.ed.gov/element/001311#Asynchronous ).');
t("Similar to courseMode, the medium or means of delivery of the program as a whole.");

// https://schema.org/educationalRole
t("educationalRole");
t("An educationalRole of an EducationalAudience.");

// https://schema.org/educationalUse
t("educationalUse");
t("The purpose of a work in the context of education; for example, 'assignment', 'group work'.");

// https://schema.org/educationRequirements
t("educationRequirements");
t("Educational background needed for the position or Occupation.");

// https://schema.org/eduQuestionType
t("eduQuestionType");
t('For questions that are part of learning resources (e.g. Quiz), eduQuestionType indicates the format of question being given. Example: "Multiple choice", "Open ended", "Flashcard".');
t("For questions that are part of learning resources (e.g. Quiz), eduQuestionType indicates the format of question being given.");

// https://schema.org/elevation
t("elevation");
t('The elevation of a location (<a href="https://en.wikipedia.org/wiki/World_Geodetic_System">WGS 84</a>). Values may be of the form \'NUMBER UNIT_OF_MEASUREMENT\' (e.g., \'1,000 m\', \'3,200 ft\') while numbers alone should be assumed to be a value in meters.');
t('The elevation of a location (<a href="https://en.wikipedia.org/wiki/World_Geodetic_System">WGS 84</a>).');

// https://schema.org/eligibilityToWorkRequirement
t("eligibilityToWorkRequirement");
t("The legal requirements such as citizenship, visa and other documentation required for an applicant to this job.");

// https://schema.org/eligibleCustomerType
t("eligibleCustomerType");
t("The type(s) of customers for which the given offer is valid.");

// https://schema.org/eligibleDuration
t("eligibleDuration");
t("The duration for which the given offer is valid.");

// https://schema.org/eligibleQuantity
t("eligibleQuantity");
t("The interval and unit of measurement of ordering quantities for which the offer or price specification is valid. This allows e.g. specifying that a certain freight charge is valid only for a certain quantity.");
t("The interval and unit of measurement of ordering quantities for which the offer or price specification is valid.");

// https://schema.org/eligibleRegion
t("eligibleRegion");
t('The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is valid.<br/><br/>

See also <a class="localLink" href="/ineligibleRegion">ineligibleRegion</a>.');
t("The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is valid.");

// https://schema.org/eligibleTransactionVolume
t("eligibleTransactionVolume");
t("The transaction volume, in a monetary unit, for which the offer or price specification is valid, e.g. for indicating a minimal purchasing volume, to express free shipping above a certain order volume, or to limit the acceptance of credit cards to purchases to a certain minimal amount.");

// https://schema.org/email
t("email");
t("Email address.");

// https://schema.org/embeddedTextCaption
t("embeddedTextCaption");
t('Represents textual captioning from a <a class="localLink" href="/MediaObject">MediaObject</a>, e.g. text of a \'meme\'.');

// https://schema.org/embedUrl
t("embedUrl");
t("A URL pointing to a player for a specific video. In general, this is the information in the <code>src</code> element of an <code>embed</code> tag and should not be the same as the content of the <code>loc</code> tag.");
t("A URL pointing to a player for a specific video.");

// https://schema.org/emissionsCO2
t("emissionsCO2");
t('The CO2 emissions in g/km. When used in combination with a QuantitativeValue, put "g/km" into the unitText property of that value, since there is no UN/CEFACT Common Code for "g/km".');
t("The CO2 emissions in g/km.");

// https://schema.org/employee
t("employee");
t("Someone working for this organization.");

// https://schema.org/employees
t("employees");
t("People working for this organization.");

// https://schema.org/employerOverview
t("employerOverview");
t("A description of the employer, career opportunities and work environment for this position.");

// https://schema.org/employmentType
t("employmentType");
t("Type of employment (e.g. full-time, part-time, contract, temporary, seasonal, internship).");

// https://schema.org/employmentUnit
t("employmentUnit");
t("Indicates the department, unit and/or facility where the employee reports and/or in which the job is to be performed.");

// https://schema.org/encodesBioChemEntity
t("encodesBioChemEntity");
t("Another BioChemEntity encoded by this one.");

// https://schema.org/encodesCreativeWork
t("encodesCreativeWork");
t("The CreativeWork encoded by this media object.");

// https://schema.org/encoding
t("encoding");
t("A media object that encodes this CreativeWork. This property is a synonym for associatedMedia.");
t("A media object that encodes this CreativeWork.");

// https://schema.org/encodingFormat
t("encodingFormat");
t('Media type typically expressed using a MIME format (see <a href="http://www.iana.org/assignments/media-types/media-types.xhtml">IANA site</a> and <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types">MDN reference</a>), e.g. application/zip for a SoftwareApplication binary, audio/mpeg for .mp3 etc.<br/><br/>

In cases where a <a class="localLink" href="/CreativeWork">CreativeWork</a> has several media type representations, <a class="localLink" href="/encoding">encoding</a> can be used to indicate each <a class="localLink" href="/MediaObject">MediaObject</a> alongside particular <a class="localLink" href="/encodingFormat">encodingFormat</a> information.<br/><br/>

Unregistered or niche encoding and file formats can be indicated instead via the most appropriate URL, e.g. defining Web page or a Wikipedia/Wikidata entry.');
t('Media type typically expressed using a MIME format (see <a href="http://www.iana.org/assignments/media-types/media-types.xhtml">IANA site</a> and <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types">MDN reference</a>), e.g. application/zip for a SoftwareApplication binary, audio/mpeg for .');

// https://schema.org/encodings
t("encodings");
t("A media object that encodes this CreativeWork.");

// https://schema.org/encodingType
t("encodingType");
t("The supported encoding type(s) for an EntryPoint request.");

// https://schema.org/endDate
t("endDate");
t('The end date and time of the item (in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 date format</a>).');

// https://schema.org/endOffset
t("endOffset");
t("The end time of the clip expressed as the number of seconds from the beginning of the work.");

// https://schema.org/endorsee
t("endorsee");
t("A sub property of participant. The person/organization being supported.");
t("A sub property of participant.");

// https://schema.org/endorsers
t("endorsers");
t("People or organizations that endorse the plan.");

// https://schema.org/endTime
t("endTime");
t("The endTime of something. For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to end. For actions that span a period of time, when the action was performed. E.g. John wrote a book from January to <em>December</em>. For media, including audio and video, it's the time offset of the end of a clip within a larger file.<br/><br/>

Note that Event uses startDate/endDate instead of startTime/endTime, even when describing dates with times. This situation may be clarified in future revisions.");
t("The endTime of something.");

// https://schema.org/energyEfficiencyScaleMax
t("energyEfficiencyScaleMax");
t("Specifies the most energy efficient class on the regulated EU energy consumption scale for the product category a product belongs to. For example, energy consumption for televisions placed on the market after January 1, 2020 is scaled from D to A+++.");
t("Specifies the most energy efficient class on the regulated EU energy consumption scale for the product category a product belongs to.");

// https://schema.org/energyEfficiencyScaleMin
t("energyEfficiencyScaleMin");
t("Specifies the least energy efficient class on the regulated EU energy consumption scale for the product category a product belongs to. For example, energy consumption for televisions placed on the market after January 1, 2020 is scaled from D to A+++.");
t("Specifies the least energy efficient class on the regulated EU energy consumption scale for the product category a product belongs to.");

// https://schema.org/engineDisplacement
t("engineDisplacement");
t('The volume swept by all of the pistons inside the cylinders of an internal combustion engine in a single movement. <br/><br/>

Typical unit code(s): CMQ for cubic centimeter, LTR for liters, INQ for cubic inches
* Note 1: You can link to information about how the given value has been determined using the <a class="localLink" href="/valueReference">valueReference</a> property.
* Note 2: You can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.');
t("The volume swept by all of the pistons inside the cylinders of an internal combustion engine in a single movement.");

// https://schema.org/enginePower
t("enginePower");
t('The power of the vehicle\'s engine.
    Typical unit code(s): KWT for kilowatt, BHP for brake horsepower, N12 for metric horsepower (PS, with 1 PS = 735,49875 W)<br/><br/>

<ul>
<li>Note 1: There are many different ways of measuring an engine\'s power. For an overview, see  <a href="http://en.wikipedia.org/wiki/Horsepower#Engine_power_test_codes">http://en.wikipedia.org/wiki/Horsepower#Engine_power_test_codes</a>.</li>
<li>Note 2: You can link to information about how the given value has been determined using the <a class="localLink" href="/valueReference">valueReference</a> property.</li>
<li>Note 3: You can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.</li>
</ul>');
t("The power of the vehicle's engine.");

// https://schema.org/engineType
t("engineType");
t("The type of engine or engines powering the vehicle.");

// https://schema.org/entertainmentBusiness
t("entertainmentBusiness");
t("A sub property of location. The entertainment business where the action occurred.");
t("A sub property of location.");

// https://schema.org/epidemiology
t("epidemiology");
t("The characteristics of associated patients, such as age, gender, race etc.");

// https://schema.org/episode
t("episode");
t("An episode of a TV, radio or game media within a series or season.");

// https://schema.org/episodeNumber
t("episodeNumber");
t("Position of the episode within an ordered group of episodes.");

// https://schema.org/episodes
t("episodes");
t("An episode of a TV/radio series or season.");

// https://schema.org/equal
t("equal");
t("This ordering relation for qualitative values indicates that the subject is equal to the object.");

// https://schema.org/error
t("error");
t("For failed actions, more information on the cause of the failure.");

// https://schema.org/estimatedCost
t("estimatedCost");
t("The estimated cost of the supply or supplies consumed when performing instructions.");

// https://schema.org/estimatedFlightDuration
t("estimatedFlightDuration");
t("The estimated time the flight will take.");

// https://schema.org/estimatedSalary
t("estimatedSalary");
t("An estimated salary for a job posting or occupation, based on a variety of variables including, but not limited to industry, job title, and location. Estimated salaries  are often computed by outside organizations rather than the hiring organization, who may not have committed to the estimated value.");
t("An estimated salary for a job posting or occupation, based on a variety of variables including, but not limited to industry, job title, and location.");

// https://schema.org/estimatesRiskOf
t("estimatesRiskOf");
t("The condition, complication, or symptom whose risk is being estimated.");

// https://schema.org/ethicsPolicy
t("ethicsPolicy");
t('Statement about ethics policy, e.g. of a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a> regarding journalistic and publishing practices, or of a <a class="localLink" href="/Restaurant">Restaurant</a>, a page describing food source policies. In the case of a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>, an ethicsPolicy is typically a statement describing the personal, organizational, and corporate standards of behavior expected by the organization.');
t('Statement about ethics policy, e.g. of a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a> regarding journalistic and publishing practices, or of a <a class="localLink" href="/Restaurant">Restaurant</a>, a page describing food source policies.');

// https://schema.org/event
t("event");
t("Upcoming or past event associated with this place, organization, or action.");

// https://schema.org/eventAttendanceMode
t("eventAttendanceMode");
t("The eventAttendanceMode of an event indicates whether it occurs online, offline, or a mix.");

// https://schema.org/events
t("events");
t("Upcoming or past events associated with this place or organization.");

// https://schema.org/eventSchedule
t("eventSchedule");
t('Associates an <a class="localLink" href="/Event">Event</a> with a <a class="localLink" href="/Schedule">Schedule</a>. There are circumstances where it is preferable to share a schedule for a series of
      repeating events rather than data on the individual events themselves. For example, a website or application might prefer to publish a schedule for a weekly
      gym class rather than provide data on every event. A schedule could be processed by applications to add forthcoming events to a calendar. An <a class="localLink" href="/Event">Event</a> that
      is associated with a <a class="localLink" href="/Schedule">Schedule</a> using this property should not have <a class="localLink" href="/startDate">startDate</a> or <a class="localLink" href="/endDate">endDate</a> properties. These are instead defined within the associated
      <a class="localLink" href="/Schedule">Schedule</a>, this avoids any ambiguity for clients using the data. The property might have repeated values to specify different schedules, e.g. for different months
      or seasons.');
t('Associates an <a class="localLink" href="/Event">Event</a> with a <a class="localLink" href="/Schedule">Schedule</a>.');

// https://schema.org/eventStatus
t("eventStatus");
t("An eventStatus of an event represents its status; particularly useful when an event is cancelled or rescheduled.");

// https://schema.org/evidenceLevel
t("evidenceLevel");
t("Strength of evidence of the data used to formulate the guideline (enumerated).");

// https://schema.org/evidenceOrigin
t("evidenceOrigin");
t("Source of the data used to formulate the guidance, e.g. RCT, consensus opinion, etc.");

// https://schema.org/exampleOfWork
t("exampleOfWork");
t("A creative work that this work is an example/instance/realization/derivation of.");

// https://schema.org/exceptDate
t("exceptDate");
t('Defines a <a class="localLink" href="/Date">Date</a> or <a class="localLink" href="/DateTime">DateTime</a> during which a scheduled <a class="localLink" href="/Event">Event</a> will not take place. The property allows exceptions to
      a <a class="localLink" href="/Schedule">Schedule</a> to be specified. If an exception is specified as a <a class="localLink" href="/DateTime">DateTime</a> then only the event that would have started at that specific date and time
      should be excluded from the schedule. If an exception is specified as a <a class="localLink" href="/Date">Date</a> then any event that is scheduled for that 24 hour period should be
      excluded from the schedule. This allows a whole day to be excluded from the schedule without having to itemise every scheduled event.');
t('Defines a <a class="localLink" href="/Date">Date</a> or <a class="localLink" href="/DateTime">DateTime</a> during which a scheduled <a class="localLink" href="/Event">Event</a> will not take place.');

// https://schema.org/exchangeRateSpread
t("exchangeRateSpread");
t("The difference between the price at which a broker or other intermediary buys and sells foreign currency.");

// https://schema.org/executableLibraryName
t("executableLibraryName");
t("Library file name, e.g., mscorlib.dll, system.web.dll.");

// https://schema.org/exerciseCourse
t("exerciseCourse");
t("A sub property of location. The course where this action was taken.");
t("A sub property of location.");

// https://schema.org/exercisePlan
t("exercisePlan");
t("A sub property of instrument. The exercise plan used on this action.");
t("A sub property of instrument.");

// https://schema.org/exerciseRelatedDiet
t("exerciseRelatedDiet");
t("A sub property of instrument. The diet used in this action.");
t("A sub property of instrument.");

// https://schema.org/exerciseType
t("exerciseType");
t("Type(s) of exercise or activity, such as strength training, flexibility training, aerobics, cardiac rehabilitation, etc.");

// https://schema.org/exifData
t("exifData");
t("exif data for this object.");

// https://schema.org/expectedArrivalFrom
t("expectedArrivalFrom");
t("The earliest date the package may arrive.");

// https://schema.org/expectedArrivalUntil
t("expectedArrivalUntil");
t("The latest date the package may arrive.");

// https://schema.org/expectedPrognosis
t("expectedPrognosis");
t("The likely outcome in either the short term or long term of the medical condition.");

// https://schema.org/expectsAcceptanceOf
t("expectsAcceptanceOf");
t("An Offer which must be accepted before the user can perform the Action. For example, the user may need to buy a movie before being able to watch it.");
t("An Offer which must be accepted before the user can perform the Action.");

// https://schema.org/experienceInPlaceOfEducation
t("experienceInPlaceOfEducation");
t('Indicates whether a <a class="localLink" href="/JobPosting">JobPosting</a> will accept experience (as indicated by <a class="localLink" href="/OccupationalExperienceRequirements">OccupationalExperienceRequirements</a>) in place of its formal educational qualifications (as indicated by <a class="localLink" href="/educationRequirements">educationRequirements</a>). If true, indicates that satisfying one of these requirements is sufficient.');
t('Indicates whether a <a class="localLink" href="/JobPosting">JobPosting</a> will accept experience (as indicated by <a class="localLink" href="/OccupationalExperienceRequirements">OccupationalExperienceRequirements</a>) in place of its formal educational qualifications (as indicated by <a class="localLink" href="/educationRequirements">educationRequirements</a>).');

// https://schema.org/experienceRequirements
t("experienceRequirements");
t("Description of skills and experience needed for the position or Occupation.");

// https://schema.org/expertConsiderations
t("expertConsiderations");
t("Medical expert advice related to the plan.");

// https://schema.org/expires
t("expires");
t('Date the content expires and is no longer useful or available. For example a <a class="localLink" href="/VideoObject">VideoObject</a> or <a class="localLink" href="/NewsArticle">NewsArticle</a> whose availability or relevance is time-limited, a <a class="localLink" href="/ClaimReview">ClaimReview</a> fact check whose publisher wants to indicate that it may no longer be relevant (or helpful to highlight) after some date, or a <a class="localLink" href="/Certification">Certification</a> the validity has expired.');
t("Date the content expires and is no longer useful or available.");

// https://schema.org/expressedIn
t("expressedIn");
t("Tissue, organ, biological sample, etc in which activity of this gene has been observed experimentally. For example brain, digestive system.");
t("Tissue, organ, biological sample, etc in which activity of this gene has been observed experimentally.");

// https://schema.org/familyName
t("familyName");
t("Family name. In the U.S., the last name of a Person.");
t("Family name.");

// https://schema.org/fatContent
t("fatContent");
t("The number of grams of fat.");

// https://schema.org/faxNumber
t("faxNumber");
t("The fax number.");

// https://schema.org/featureList
t("featureList");
t("Features or modules provided by this application (and possibly required by other applications).");

// https://schema.org/feesAndCommissionsSpecification
t("feesAndCommissionsSpecification");
t("Description of fees, commissions, and other terms applied either to a class of financial product, or by a financial service organization.");

// https://schema.org/fiberContent
t("fiberContent");
t("The number of grams of fiber.");

// https://schema.org/fileFormat
t("fileFormat");
t('Media type, typically MIME format (see <a href="http://www.iana.org/assignments/media-types/media-types.xhtml">IANA site</a>) of the content, e.g. application/zip of a SoftwareApplication binary. In cases where a CreativeWork has several media type representations, \'encoding\' can be used to indicate each MediaObject alongside particular fileFormat information. Unregistered or niche file formats can be indicated instead via the most appropriate URL, e.g. defining Web page or a Wikipedia entry.');
t('Media type, typically MIME format (see <a href="http://www.iana.org/assignments/media-types/media-types.xhtml">IANA site</a>) of the content, e.g. application/zip of a SoftwareApplication binary.');

// https://schema.org/fileSize
t("fileSize");
t("Size of the application / package (e.g. 18MB). In the absence of a unit (MB, KB etc.), KB will be assumed.");
t("Size of the application / package (e.g. 18MB).");

// https://schema.org/financialAidEligible
t("financialAidEligible");
t("A financial aid type or program which students may use to pay for tuition or fees associated with the program.");

// https://schema.org/firstAppearance
t("firstAppearance");
t('Indicates the first known occurrence of a <a class="localLink" href="/Claim">Claim</a> in some <a class="localLink" href="/CreativeWork">CreativeWork</a>.');

// https://schema.org/firstPerformance
t("firstPerformance");
t("The date and place the work was first performed.");

// https://schema.org/flightDistance
t("flightDistance");
t("The distance of the flight.");

// https://schema.org/flightNumber
t("flightNumber");
t("The unique identifier for a flight including the airline IATA code. For example, if describing United flight 110, where the IATA code for United is 'UA', the flightNumber is 'UA110'.");
t("The unique identifier for a flight including the airline IATA code.");

// https://schema.org/floorLevel
t("floorLevel");
t('The floor level for an <a class="localLink" href="/Accommodation">Accommodation</a> in a multi-storey building. Since counting
  systems <a href="https://en.wikipedia.org/wiki/Storey#Consecutive_number_floor_designations">vary internationally</a>, the local system should be used where possible.');
t('The floor level for an <a class="localLink" href="/Accommodation">Accommodation</a> in a multi-storey building.');

// https://schema.org/floorLimit
t("floorLimit");
t("A floor limit is the amount of money above which credit card transactions must be authorized.");

// https://schema.org/floorSize
t("floorSize");
t("The size of the accommodation, e.g. in square meter or squarefoot.
Typical unit code(s): MTK for square meter, FTK for square foot, or YDK for square yard.");
t("The size of the accommodation, e.g. in square meter or squarefoot.");

// https://schema.org/followee
t("followee");
t("A sub property of object. The person or organization being followed.");
t("A sub property of object.");

// https://schema.org/follows
t("follows");
t("The most generic uni-directional social relation.");

// https://schema.org/followup
t("followup");
t("Typical or recommended followup care after the procedure is performed.");

// https://schema.org/foodEstablishment
t("foodEstablishment");
t("A sub property of location. The specific food establishment where the action occurred.");
t("A sub property of location.");

// https://schema.org/foodEvent
t("foodEvent");
t("A sub property of location. The specific food event where the action occurred.");
t("A sub property of location.");

// https://schema.org/foodWarning
t("foodWarning");
t("Any precaution, guidance, contraindication, etc. related to consumption of specific foods while taking this drug.");

// https://schema.org/founder
t("founder");
t("A person who founded this organization.");

// https://schema.org/founders
t("founders");
t("A person who founded this organization.");

// https://schema.org/foundingDate
t("foundingDate");
t("The date that this organization was founded.");

// https://schema.org/foundingLocation
t("foundingLocation");
t("The place where the Organization was founded.");

// https://schema.org/free
t("free");
t("A flag to signal that the item, event, or place is accessible for free.");

// https://schema.org/freeShippingThreshold
t("freeShippingThreshold");
t('A monetary value above (or at) which the shipping rate becomes free. Intended to be used via an <a class="localLink" href="/OfferShippingDetails">OfferShippingDetails</a> with <a class="localLink" href="/shippingSettingsLink">shippingSettingsLink</a> matching this <a class="localLink" href="/ShippingRateSettings">ShippingRateSettings</a>.');
t("A monetary value above (or at) which the shipping rate becomes free.");

// https://schema.org/frequency
t("frequency");
t("How often the dose is taken, e.g. 'daily'.");

// https://schema.org/fromLocation
t("fromLocation");
t("A sub property of location. The original location of the object or the agent before the action.");
t("A sub property of location.");

// https://schema.org/fuelCapacity
t("fuelCapacity");
t("The capacity of the fuel tank or in the case of electric cars, the battery. If there are multiple components for storage, this should indicate the total of all storage of the same type.<br/><br/>

Typical unit code(s): LTR for liters, GLL of US gallons, GLI for UK / imperial gallons, AMH for ampere-hours (for electrical vehicles).");
t("The capacity of the fuel tank or in the case of electric cars, the battery.");

// https://schema.org/fuelConsumption
t("fuelConsumption");
t('The amount of fuel consumed for traveling a particular distance or temporal duration with the given vehicle (e.g. liters per 100 km).<br/><br/>

<ul>
<li>Note 1: There are unfortunately no standard unit codes for liters per 100 km.  Use <a class="localLink" href="/unitText">unitText</a> to indicate the unit of measurement, e.g. L/100 km.</li>
<li>Note 2: There are two ways of indicating the fuel consumption, <a class="localLink" href="/fuelConsumption">fuelConsumption</a> (e.g. 8 liters per 100 km) and <a class="localLink" href="/fuelEfficiency">fuelEfficiency</a> (e.g. 30 miles per gallon). They are reciprocal.</li>
<li>Note 3: Often, the absolute value is useful only when related to driving speed ("at 80 km/h") or usage pattern ("city traffic"). You can use <a class="localLink" href="/valueReference">valueReference</a> to link the value for the fuel consumption to another value.</li>
</ul>');
t("The amount of fuel consumed for traveling a particular distance or temporal duration with the given vehicle (e.g. liters per 100 km).");

// https://schema.org/fuelEfficiency
t("fuelEfficiency");
t('The distance traveled per unit of fuel used; most commonly miles per gallon (mpg) or kilometers per liter (km/L).<br/><br/>

<ul>
<li>Note 1: There are unfortunately no standard unit codes for miles per gallon or kilometers per liter. Use <a class="localLink" href="/unitText">unitText</a> to indicate the unit of measurement, e.g. mpg or km/L.</li>
<li>Note 2: There are two ways of indicating the fuel consumption, <a class="localLink" href="/fuelConsumption">fuelConsumption</a> (e.g. 8 liters per 100 km) and <a class="localLink" href="/fuelEfficiency">fuelEfficiency</a> (e.g. 30 miles per gallon). They are reciprocal.</li>
<li>Note 3: Often, the absolute value is useful only when related to driving speed ("at 80 km/h") or usage pattern ("city traffic"). You can use <a class="localLink" href="/valueReference">valueReference</a> to link the value for the fuel economy to another value.</li>
</ul>');
t("The distance traveled per unit of fuel used; most commonly miles per gallon (mpg) or kilometers per liter (km/L).");

// https://schema.org/fuelType
t("fuelType");
t("The type of fuel suitable for the engine or engines of the vehicle. If the vehicle has only one engine, this property can be attached directly to the vehicle.");
t("The type of fuel suitable for the engine or engines of the vehicle.");

// https://schema.org/functionalClass
t("functionalClass");
t("The degree of mobility the joint allows.");

// https://schema.org/fundedItem
t("fundedItem");
t('Indicates something directly or indirectly funded or sponsored through a <a class="localLink" href="/Grant">Grant</a>. See also <a class="localLink" href="/ownershipFundingInfo">ownershipFundingInfo</a>.');
t('Indicates something directly or indirectly funded or sponsored through a <a class="localLink" href="/Grant">Grant</a>.');

// https://schema.org/funder
t("funder");
t("A person or organization that supports (sponsors) something through some kind of financial contribution.");

// https://schema.org/funding
t("funding");
t('A <a class="localLink" href="/Grant">Grant</a> that directly or indirectly provide funding or sponsorship for this item. See also <a class="localLink" href="/ownershipFundingInfo">ownershipFundingInfo</a>.');
t('A <a class="localLink" href="/Grant">Grant</a> that directly or indirectly provide funding or sponsorship for this item.');

// https://schema.org/game
t("game");
t("Video game which is played on this server.");

// https://schema.org/gameAvailabilityType
t("gameAvailabilityType");
t("Indicates the availability type of the game content associated with this action, such as whether it is a full version or a demo.");

// https://schema.org/gameEdition
t("gameEdition");
t("The edition of a video game.");

// https://schema.org/gameItem
t("gameItem");
t("An item is an object within the game world that can be collected by a player or, occasionally, a non-player character.");

// https://schema.org/gameLocation
t("gameLocation");
t("Real or fictional location of the game (or part of game).");

// https://schema.org/gamePlatform
t("gamePlatform");
t('The electronic systems used to play <a href="http://en.wikipedia.org/wiki/Category:Video_game_platforms">video games</a>.');

// https://schema.org/gameServer
t("gameServer");
t("The server on which  it is possible to play the game.");

// https://schema.org/gameTip
t("gameTip");
t("Links to tips, tactics, etc.");

// https://schema.org/gender
t("gender");
t('Gender of something, typically a <a class="localLink" href="/Person">Person</a>, but possibly also fictional characters, animals, etc. While https://schema.org/Male and https://schema.org/Female may be used, text strings are also acceptable for people who do not identify as a binary gender. The <a class="localLink" href="/gender">gender</a> property can also be used in an extended sense to cover e.g. the gender of sports teams. As with the gender of individuals, we do not try to enumerate all possibilities. A mixed-gender <a class="localLink" href="/SportsTeam">SportsTeam</a> can be indicated with a text value of "Mixed".');
t('Gender of something, typically a <a class="localLink" href="/Person">Person</a>, but possibly also fictional characters, animals, etc. While https://schema.org/Male and https://schema.org/Female may be used, text strings are also acceptable for people who do not identify as a binary gender.');

// https://schema.org/genre
t("genre");
t("Genre of the creative work, broadcast channel or group.");

// https://schema.org/geo
t("geo");
t("The geo coordinates of the place.");

// https://schema.org/geoContains
t("geoContains");
t('Represents a relationship between two geometries (or the places they represent), relating a containing geometry to a contained geometry. "a contains b iff no points of b lie in the exterior of a, and at least one point of the interior of b lies in the interior of a". As defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.');
t("Represents a relationship between two geometries (or the places they represent), relating a containing geometry to a contained geometry.");

// https://schema.org/geoCoveredBy
t("geoCoveredBy");
t('Represents a relationship between two geometries (or the places they represent), relating a geometry to another that covers it. As defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.');
t("Represents a relationship between two geometries (or the places they represent), relating a geometry to another that covers it.");

// https://schema.org/geoCovers
t("geoCovers");
t('Represents a relationship between two geometries (or the places they represent), relating a covering geometry to a covered geometry. "Every point of b is a point of (the interior or boundary of) a". As defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.');
t("Represents a relationship between two geometries (or the places they represent), relating a covering geometry to a covered geometry.");

// https://schema.org/geoCrosses
t("geoCrosses");
t('Represents a relationship between two geometries (or the places they represent), relating a geometry to another that crosses it: "a crosses b: they have some but not all interior points in common, and the dimension of the intersection is less than that of at least one of them". As defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.');
t('Represents a relationship between two geometries (or the places they represent), relating a geometry to another that crosses it: "a crosses b: they have some but not all interior points in common, and the dimension of the intersection is less than that of at least one of them".');

// https://schema.org/geoDisjoint
t("geoDisjoint");
t('Represents spatial relations in which two geometries (or the places they represent) are topologically disjoint: "they have no point in common. They form a set of disconnected geometries." (A symmetric relationship, as defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.)');
t('Represents spatial relations in which two geometries (or the places they represent) are topologically disjoint: "they have no point in common.');

// https://schema.org/geoEquals
t("geoEquals");
t('Represents spatial relations in which two geometries (or the places they represent) are topologically equal, as defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>. "Two geometries are topologically equal if their interiors intersect and no part of the interior or boundary of one geometry intersects the exterior of the other" (a symmetric relationship).');
t('Represents spatial relations in which two geometries (or the places they represent) are topologically equal, as defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.');

// https://schema.org/geographicArea
t("geographicArea");
t("The geographic area associated with the audience.");

// https://schema.org/geoIntersects
t("geoIntersects");
t('Represents spatial relations in which two geometries (or the places they represent) have at least one point in common. As defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.');
t("Represents spatial relations in which two geometries (or the places they represent) have at least one point in common.");

// https://schema.org/geoMidpoint
t("geoMidpoint");
t("Indicates the GeoCoordinates at the centre of a GeoShape, e.g. GeoCircle.");

// https://schema.org/geoOverlaps
t("geoOverlaps");
t('Represents a relationship between two geometries (or the places they represent), relating a geometry to another that geospatially overlaps it, i.e. they have some but not all points in common. As defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.');
t("Represents a relationship between two geometries (or the places they represent), relating a geometry to another that geospatially overlaps it, i.e. they have some but not all points in common.");

// https://schema.org/geoRadius
t("geoRadius");
t("Indicates the approximate radius of a GeoCircle (metres unless indicated otherwise via Distance notation).");

// https://schema.org/geoTouches
t("geoTouches");
t('Represents spatial relations in which two geometries (or the places they represent) touch: "they have at least one boundary point in common, but no interior points." (A symmetric relationship, as defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.)');
t('Represents spatial relations in which two geometries (or the places they represent) touch: "they have at least one boundary point in common, but no interior points.');

// https://schema.org/geoWithin
t("geoWithin");
t('Represents a relationship between two geometries (or the places they represent), relating a geometry to one that contains it, i.e. it is inside (i.e. within) its interior. As defined in <a href="https://en.wikipedia.org/wiki/DE-9IM">DE-9IM</a>.');
t("Represents a relationship between two geometries (or the places they represent), relating a geometry to one that contains it, i.e. it is inside (i.e. within) its interior.");

// https://schema.org/gettingTestedInfo
t("gettingTestedInfo");
t('Information about getting tested (for a <a class="localLink" href="/MedicalCondition">MedicalCondition</a>), e.g. in the context of a pandemic.');

// https://schema.org/givenName
t("givenName");
t("Given name. In the U.S., the first name of a Person.");
t("Given name.");

// https://schema.org/globalLocationNumber
t("globalLocationNumber");
t('The <a href="http://www.gs1.org/gln">Global Location Number</a> (GLN, sometimes also referred to as International Location Number or ILN) of the respective organization, person, or place. The GLN is a 13-digit number used to identify parties and physical locations.');
t('The <a href="http://www.gs1.org/gln">Global Location Number</a> (GLN, sometimes also referred to as International Location Number or ILN) of the respective organization, person, or place.');

// https://schema.org/governmentBenefitsInfo
t("governmentBenefitsInfo");
t("governmentBenefitsInfo provides information about government benefits associated with a SpecialAnnouncement.");

// https://schema.org/gracePeriod
t("gracePeriod");
t("The period of time after any due date that the borrower has to fulfil its obligations before a default (failure to pay) is deemed to have occurred.");

// https://schema.org/grantee
t("grantee");
t("The person, organization, contact point, or audience that has been granted this permission.");

// https://schema.org/greater
t("greater");
t("This ordering relation for qualitative values indicates that the subject is greater than the object.");

// https://schema.org/greaterOrEqual
t("greaterOrEqual");
t("This ordering relation for qualitative values indicates that the subject is greater than or equal to the object.");

// https://schema.org/gtin
t("gtin");
t('A Global Trade Item Number (<a href="https://www.gs1.org/standards/id-keys/gtin">GTIN</a>). GTINs identify trade items, including products and services, using numeric identification codes.<br/><br/>

The GS1 <a href="https://www.gs1.org/standards/Digital-Link/">digital link specifications</a> express GTINs as URLs (URIs, IRIs, etc.). Details including regular expression examples can be found in, Section 6 of the GS1 URI Syntax specification; see also <a href="https://github.com/schemaorg/schemaorg/issues/3156#issuecomment-1209522809">schema.org tracking issue</a> for schema.org-specific discussion. A correct <a class="localLink" href="/gtin">gtin</a> value should be a valid GTIN, which means that it should be an all-numeric string of either 8, 12, 13 or 14 digits, or a "GS1 Digital Link" URL based on such a string. The numeric component should also have a <a href="https://www.gs1.org/services/check-digit-calculator">valid GS1 check digit</a> and meet the other rules for valid GTINs. See also <a href="http://www.gs1.org/barcodes/technical/idkeys/gtin">GS1\'s GTIN Summary</a> and <a href="https://en.wikipedia.org/wiki/Global_Trade_Item_Number">Wikipedia</a> for more details. Left-padding of the gtin values is not required or encouraged. The <a class="localLink" href="/gtin">gtin</a> property generalizes the earlier <a class="localLink" href="/gtin8">gtin8</a>, <a class="localLink" href="/gtin12">gtin12</a>, <a class="localLink" href="/gtin13">gtin13</a>, and <a class="localLink" href="/gtin14">gtin14</a> properties.<br/><br/>

Note also that this is a definition for how to include GTINs in Schema.org data, and not a definition of GTINs in general - see the GS1 documentation for authoritative details.');
t('A Global Trade Item Number (<a href="https://www.gs1.org/standards/id-keys/gtin">GTIN</a>).');

// https://schema.org/gtin12
t("gtin12");
t('The GTIN-12 code of the product, or the product to which the offer refers. The GTIN-12 is the 12-digit GS1 Identification Key composed of a U.P.C. Company Prefix, Item Reference, and Check Digit used to identify trade items. See <a href="http://www.gs1.org/barcodes/technical/idkeys/gtin">GS1 GTIN Summary</a> for more details.');
t("The GTIN-12 code of the product, or the product to which the offer refers.");

// https://schema.org/gtin13
t("gtin13");
t('The GTIN-13 code of the product, or the product to which the offer refers. This is equivalent to 13-digit ISBN codes and EAN UCC-13. Former 12-digit UPC codes can be converted into a GTIN-13 code by simply adding a preceding zero. See <a href="http://www.gs1.org/barcodes/technical/idkeys/gtin">GS1 GTIN Summary</a> for more details.');
t("The GTIN-13 code of the product, or the product to which the offer refers.");

// https://schema.org/gtin14
t("gtin14");
t('The GTIN-14 code of the product, or the product to which the offer refers. See <a href="http://www.gs1.org/barcodes/technical/idkeys/gtin">GS1 GTIN Summary</a> for more details.');
t("The GTIN-14 code of the product, or the product to which the offer refers.");

// https://schema.org/gtin8
t("gtin8");
t('The GTIN-8 code of the product, or the product to which the offer refers. This code is also known as EAN/UCC-8 or 8-digit EAN. See <a href="http://www.gs1.org/barcodes/technical/idkeys/gtin">GS1 GTIN Summary</a> for more details.');
t("The GTIN-8 code of the product, or the product to which the offer refers.");

// https://schema.org/guideline
t("guideline");
t("A medical guideline related to this entity.");

// https://schema.org/guidelineDate
t("guidelineDate");
t("Date on which this guideline's recommendation was made.");

// https://schema.org/guidelineSubject
t("guidelineSubject");
t("The medical conditions, treatments, etc. that are the subject of the guideline.");

// https://schema.org/handlingTime
t("handlingTime");
t('The typical delay between the receipt of the order and the goods either leaving the warehouse or being prepared for pickup, in case the delivery method is on site pickup. Typical properties: minValue, maxValue, unitCode (d for DAY).  This is by common convention assumed to mean business days (if a unitCode is used, coded as "d"), i.e. only counting days when the business normally operates.');
t("The typical delay between the receipt of the order and the goods either leaving the warehouse or being prepared for pickup, in case the delivery method is on site pickup.");

// https://schema.org/hasAdultConsideration
t("hasAdultConsideration");
t("Used to tag an item to be intended or suitable for consumption or use by adults only.");

// https://schema.org/hasBioChemEntityPart
t("hasBioChemEntityPart");
t("Indicates a BioChemEntity that (in some sense) has this BioChemEntity as a part.");

// https://schema.org/hasBioPolymerSequence
t("hasBioPolymerSequence");
t("A symbolic representation of a BioChemEntity. For example, a nucleotide sequence of a Gene or an amino acid sequence of a Protein.");
t("A symbolic representation of a BioChemEntity.");

// https://schema.org/hasBroadcastChannel
t("hasBroadcastChannel");
t("A broadcast channel of a broadcast service.");

// https://schema.org/hasCategoryCode
t("hasCategoryCode");
t("A Category code contained in this code set.");

// https://schema.org/hasCertification
t("hasCertification");
t("Certification information about a product, organization, service, place, or person.");

// https://schema.org/hasCourse
t("hasCourse");
t("A course or class that is one of the learning opportunities that constitute an educational / occupational program. No information is implied about whether the course is mandatory or optional; no guarantee is implied about whether the course will be available to everyone on the program.");
t("A course or class that is one of the learning opportunities that constitute an educational / occupational program.");

// https://schema.org/hasCourseInstance
t("hasCourseInstance");
t("An offering of the course at a specific time and place or through specific media or mode of study or to a specific section of students.");

// https://schema.org/hasCredential
t("hasCredential");
t("A credential awarded to the Person or Organization.");

// https://schema.org/hasDefinedTerm
t("hasDefinedTerm");
t("A Defined Term contained in this term set.");

// https://schema.org/hasDeliveryMethod
t("hasDeliveryMethod");
t("Method used for delivery or shipping.");

// https://schema.org/hasDigitalDocumentPermission
t("hasDigitalDocumentPermission");
t('A permission related to the access to this document (e.g. permission to read or write an electronic document). For a public document, specify a grantee with an Audience with audienceType equal to "public".');
t("A permission related to the access to this document (e.g. permission to read or write an electronic document).");

// https://schema.org/hasDriveThroughService
t("hasDriveThroughService");
t('Indicates whether some facility (e.g. <a class="localLink" href="/FoodEstablishment">FoodEstablishment</a>, <a class="localLink" href="/CovidTestingFacility">CovidTestingFacility</a>) offers a service that can be used by driving through in a car. In the case of <a class="localLink" href="/CovidTestingFacility">CovidTestingFacility</a> such facilities could potentially help with social distancing from other potentially-infected users.');
t('Indicates whether some facility (e.g. <a class="localLink" href="/FoodEstablishment">FoodEstablishment</a>, <a class="localLink" href="/CovidTestingFacility">CovidTestingFacility</a>) offers a service that can be used by driving through in a car.');

// https://schema.org/hasEnergyConsumptionDetails
t("hasEnergyConsumptionDetails");
t('Defines the energy efficiency Category (also known as "class" or "rating") for a product according to an international energy efficiency standard.');

// https://schema.org/hasEnergyEfficiencyCategory
t("hasEnergyEfficiencyCategory");
t("Defines the energy efficiency Category (which could be either a rating out of range of values or a yes/no certification) for a product according to an international energy efficiency standard.");

// https://schema.org/hasHealthAspect
t("hasHealthAspect");
t('Indicates the aspect or aspects specifically addressed in some <a class="localLink" href="/HealthTopicContent">HealthTopicContent</a>. For example, that the content is an overview, or that it talks about treatment, self-care, treatments or their side-effects.');
t('Indicates the aspect or aspects specifically addressed in some <a class="localLink" href="/HealthTopicContent">HealthTopicContent</a>.');

// https://schema.org/hasMap
t("hasMap");
t("A URL to a map of the place.");

// https://schema.org/hasMeasurement
t("hasMeasurement");
t("A measurement of an item, For example, the inseam of pants, the wheel size of a bicycle, the gauge of a screw, or the carbon footprint measured for certification by an authority. Usually an exact measurement, but can also be a range of measurements for adjustable products, for example belts and ski bindings.");
t("A measurement of an item, For example, the inseam of pants, the wheel size of a bicycle, the gauge of a screw, or the carbon footprint measured for certification by an authority.");

// https://schema.org/hasMenu
t("hasMenu");
t("Either the actual menu as a structured representation, as text, or a URL of the menu.");

// https://schema.org/hasMenuItem
t("hasMenuItem");
t("A food or drink item contained in a menu or menu section.");

// https://schema.org/hasMenuSection
t("hasMenuSection");
t("A subgrouping of the menu (by dishes, course, serving time period, etc.).");

// https://schema.org/hasMerchantReturnPolicy
t("hasMerchantReturnPolicy");
t("Specifies a MerchantReturnPolicy that may be applicable.");

// https://schema.org/hasMolecularFunction
t("hasMolecularFunction");
t("Molecular function performed by this BioChemEntity; please use PropertyValue if you want to include any evidence.");

// https://schema.org/hasOccupation
t("hasOccupation");
t("The Person's occupation. For past professions, use Role for expressing dates.");
t("The Person's occupation.");

// https://schema.org/hasOfferCatalog
t("hasOfferCatalog");
t("Indicates an OfferCatalog listing for this Organization, Person, or Service.");

// https://schema.org/hasPart
t("hasPart");
t("Indicates an item or CreativeWork that is part of this item, or CreativeWork (in some sense).");

// https://schema.org/hasPOS
t("hasPOS");
t("Points-of-Sales operated by the organization or person.");

// https://schema.org/hasProductReturnPolicy
t("hasProductReturnPolicy");
t("Indicates a ProductReturnPolicy that may be applicable.");

// https://schema.org/hasRepresentation
t("hasRepresentation");
t("A common representation such as a protein sequence or chemical structure for this entity. For images use schema.org/image.");
t("A common representation such as a protein sequence or chemical structure for this entity.");

// https://schema.org/hasVariant
t("hasVariant");
t('Indicates a <a class="localLink" href="/Product">Product</a> that is a member of this <a class="localLink" href="/ProductGroup">ProductGroup</a> (or <a class="localLink" href="/ProductModel">ProductModel</a>).');

// https://schema.org/headline
t("headline");
t("Headline of the article.");

// https://schema.org/healthcareReportingData
t("healthcareReportingData");
t('Indicates data describing a hospital, e.g. a CDC <a class="localLink" href="/CDCPMDRecord">CDCPMDRecord</a> or as some kind of <a class="localLink" href="/Dataset">Dataset</a>.');

// https://schema.org/healthCondition
t("healthCondition");
t("Specifying the health condition(s) of a patient, medical study, or other target audience.");

// https://schema.org/healthPlanCoinsuranceOption
t("healthPlanCoinsuranceOption");
t("Whether the coinsurance applies before or after deductible, etc. TODO: Is this a closed set?");

// https://schema.org/healthPlanCoinsuranceRate
t("healthPlanCoinsuranceRate");
t("The rate of coinsurance expressed as a number between 0.0 and 1.0.");

// https://schema.org/healthPlanCopay
t("healthPlanCopay");
t("The copay amount.");

// https://schema.org/healthPlanCopayOption
t("healthPlanCopayOption");
t("Whether the copay is before or after deductible, etc. TODO: Is this a closed set?");

// https://schema.org/healthPlanCostSharing
t("healthPlanCostSharing");
t("The costs to the patient for services under this network or formulary.");

// https://schema.org/healthPlanDrugOption
t("healthPlanDrugOption");
t("TODO.");

// https://schema.org/healthPlanDrugTier
t("healthPlanDrugTier");
t("The tier(s) of drugs offered by this formulary or insurance plan.");

// https://schema.org/healthPlanId
t("healthPlanId");
t("The 14-character, HIOS-generated Plan ID number. (Plan IDs must be unique, even across different markets.)");
t("The 14-character, HIOS-generated Plan ID number.");

// https://schema.org/healthPlanMarketingUrl
t("healthPlanMarketingUrl");
t("The URL that goes directly to the plan brochure for the specific standard plan or plan variation.");

// https://schema.org/healthPlanNetworkId
t("healthPlanNetworkId");
t("Name or unique ID of network. (Networks are often reused across different insurance plans.)");
t("Name or unique ID of network.");

// https://schema.org/healthPlanNetworkTier
t("healthPlanNetworkTier");
t("The tier(s) for this network.");

// https://schema.org/healthPlanPharmacyCategory
t("healthPlanPharmacyCategory");
t("The category or type of pharmacy associated with this cost sharing.");

// https://schema.org/height
t("height");
t("The height of the item.");

// https://schema.org/highPrice
t("highPrice");
t("The highest price of all offers available.<br/><br/>

Usage guidelines:<br/><br/>

<ul>
<li>Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.</li>
<li>Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.</li>
</ul>");
t("The highest price of all offers available.");

// https://schema.org/hiringOrganization
t("hiringOrganization");
t("Organization or Person offering the job position.");

// https://schema.org/holdingArchive
t("holdingArchive");
t('<a class="localLink" href="/ArchiveOrganization">ArchiveOrganization</a> that holds, keeps or maintains the <a class="localLink" href="/ArchiveComponent">ArchiveComponent</a>.');

// https://schema.org/homeLocation
t("homeLocation");
t("A contact location for a person's residence.");

// https://schema.org/homeTeam
t("homeTeam");
t("The home team in a sports event.");

// https://schema.org/honorificPrefix
t("honorificPrefix");
t("An honorific prefix preceding a Person's name such as Dr/Mrs/Mr.");

// https://schema.org/honorificSuffix
t("honorificSuffix");
t("An honorific suffix following a Person's name such as M.D./PhD/MSCSW.");

// https://schema.org/hospitalAffiliation
t("hospitalAffiliation");
t("A hospital with which the physician or office is affiliated.");

// https://schema.org/hostingOrganization
t("hostingOrganization");
t("The organization (airline, travelers' club, etc.) the membership is made with.");

// https://schema.org/hoursAvailable
t("hoursAvailable");
t("The hours during which this service or contact is available.");

// https://schema.org/howPerformed
t("howPerformed");
t("How the procedure is performed.");

// https://schema.org/httpMethod
t("httpMethod");
t("An HTTP method that specifies the appropriate HTTP method for a request to an HTTP EntryPoint. Values are capitalized strings as used in HTTP.");
t("An HTTP method that specifies the appropriate HTTP method for a request to an HTTP EntryPoint.");

// https://schema.org/iataCode
t("iataCode");
t("IATA identifier for an airline or airport.");

// https://schema.org/icaoCode
t("icaoCode");
t("ICAO identifier for an airport.");

// https://schema.org/identifier
t("identifier");
t('The identifier property represents any kind of identifier for any kind of <a class="localLink" href="/Thing">Thing</a>, such as ISBNs, GTIN codes, UUIDs etc. Schema.org provides dedicated properties for representing many of these, either as textual strings or as URL (URI) links. See <a href="/docs/datamodel.html#identifierBg">background notes</a> for more details.');
t('The identifier property represents any kind of identifier for any kind of <a class="localLink" href="/Thing">Thing</a>, such as ISBNs, GTIN codes, UUIDs etc. Schema.org provides dedicated properties for representing many of these, either as textual strings or as URL (URI) links.');

// https://schema.org/identifyingExam
t("identifyingExam");
t("A physical examination that can identify this sign.");

// https://schema.org/identifyingTest
t("identifyingTest");
t("A diagnostic test that can identify this sign.");

// https://schema.org/illustrator
t("illustrator");
t("The illustrator of the book.");

// https://schema.org/image
t("image");
t('An image of the item. This can be a <a class="localLink" href="/URL">URL</a> or a fully described <a class="localLink" href="/ImageObject">ImageObject</a>.');
t("An image of the item.");

// https://schema.org/imagingTechnique
t("imagingTechnique");
t("Imaging technique used.");

// https://schema.org/inAlbum
t("inAlbum");
t("The album to which this recording belongs.");

// https://schema.org/inBroadcastLineup
t("inBroadcastLineup");
t("The CableOrSatelliteService offering the channel.");

// https://schema.org/incentiveCompensation
t("incentiveCompensation");
t("Description of bonus and commission compensation aspects of the job.");

// https://schema.org/incentives
t("incentives");
t("Description of bonus and commission compensation aspects of the job.");

// https://schema.org/inChI
t("inChI");
t("Non-proprietary identifier for molecular entity that can be used in printed and electronic data sources thus enabling easier linking of diverse data compilations.");

// https://schema.org/inChIKey
t("inChIKey");
t("InChIKey is a hashed version of the full InChI (using the SHA-256 algorithm).");

// https://schema.org/includedComposition
t("includedComposition");
t("Smaller compositions included in this work (e.g. a movement in a symphony).");

// https://schema.org/includedDataCatalog
t("includedDataCatalog");
t("A data catalog which contains this dataset (this property was previously 'catalog', preferred name is now 'includedInDataCatalog').");

// https://schema.org/includedInDataCatalog
t("includedInDataCatalog");
t("A data catalog which contains this dataset.");

// https://schema.org/includedInHealthInsurancePlan
t("includedInHealthInsurancePlan");
t("The insurance plans that cover this drug.");

// https://schema.org/includedRiskFactor
t("includedRiskFactor");
t("A modifiable or non-modifiable risk factor included in the calculation, e.g. age, coexisting condition.");

// https://schema.org/includesAttraction
t("includesAttraction");
t("Attraction located at destination.");

// https://schema.org/includesHealthPlanFormulary
t("includesHealthPlanFormulary");
t("Formularies covered by this plan.");

// https://schema.org/includesHealthPlanNetwork
t("includesHealthPlanNetwork");
t("Networks covered by this plan.");

// https://schema.org/includesObject
t("includesObject");
t('This links to a node or nodes indicating the exact quantity of the products included in  an <a class="localLink" href="/Offer">Offer</a> or <a class="localLink" href="/ProductCollection">ProductCollection</a>.');

// https://schema.org/inCodeSet
t("inCodeSet");
t('A <a class="localLink" href="/CategoryCodeSet">CategoryCodeSet</a> that contains this category code.');

// https://schema.org/increasesRiskOf
t("increasesRiskOf");
t("The condition, complication, etc. influenced by this factor.");

// https://schema.org/inDefinedTermSet
t("inDefinedTermSet");
t('A <a class="localLink" href="/DefinedTermSet">DefinedTermSet</a> that contains this term.');

// https://schema.org/industry
t("industry");
t("The industry associated with the job position.");

// https://schema.org/ineligibleRegion
t("ineligibleRegion");
t('The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is not valid, e.g. a region where the transaction is not allowed.<br/><br/>

See also <a class="localLink" href="/eligibleRegion">eligibleRegion</a>.');
t("The ISO 3166-1 (ISO 3166-1 alpha-2) or ISO 3166-2 code, the place, or the GeoShape for the geo-political region(s) for which the offer or delivery charge specification is not valid, e.g. a region where the transaction is not allowed.");

// https://schema.org/infectiousAgent
t("infectiousAgent");
t("The actual infectious agent, such as a specific bacterium.");

// https://schema.org/infectiousAgentClass
t("infectiousAgentClass");
t("The class of infectious agent (bacteria, prion, etc.) that causes the disease.");

// https://schema.org/ingredients
t("ingredients");
t("A single ingredient used in the recipe, e.g. sugar, flour or garlic.");

// https://schema.org/inker
t("inker");
t("The individual who traces over the pencil drawings in ink after pencils are complete.");

// https://schema.org/inLanguage
t("inLanguage");
t('The language of the content or performance or used in an action. Please use one of the language codes from the <a href="http://tools.ietf.org/html/bcp47">IETF BCP 47 standard</a>. See also <a class="localLink" href="/availableLanguage">availableLanguage</a>.');
t("The language of the content or performance or used in an action.");

// https://schema.org/inPlaylist
t("inPlaylist");
t("The playlist to which this recording belongs.");

// https://schema.org/inProductGroupWithID
t("inProductGroupWithID");
t('Indicates the <a class="localLink" href="/productGroupID">productGroupID</a> for a <a class="localLink" href="/ProductGroup">ProductGroup</a> that this product <a class="localLink" href="/isVariantOf">isVariantOf</a>.');

// https://schema.org/insertion
t("insertion");
t("The place of attachment of a muscle, or what the muscle moves.");

// https://schema.org/installUrl
t("installUrl");
t("URL at which the app may be installed, if different from the URL of the item.");

// https://schema.org/inStoreReturnsOffered
t("inStoreReturnsOffered");
t('Are in-store returns offered? (For more advanced return methods use the <a class="localLink" href="/returnMethod">returnMethod</a> property.)');
t('Are in-store returns offered? (For more advanced return methods use the <a class="localLink" href="/returnMethod">returnMethod</a> property.');

// https://schema.org/instructor
t("instructor");
t('A person assigned to instruct or provide instructional assistance for the <a class="localLink" href="/CourseInstance">CourseInstance</a>.');

// https://schema.org/instrument
t("instrument");
t("The object that helped the agent perform the action. E.g. John wrote a book with <em>a pen</em>.");
t("The object that helped the agent perform the action.");

// https://schema.org/inSupportOf
t("inSupportOf");
t("Qualification, candidature, degree, application that Thesis supports.");

// https://schema.org/intensity
t("intensity");
t("Quantitative measure gauging the degree of force involved in the exercise, for example, heartbeats per minute. May include the velocity of the movement.");
t("Quantitative measure gauging the degree of force involved in the exercise, for example, heartbeats per minute.");

// https://schema.org/interactingDrug
t("interactingDrug");
t("Another drug that is known to interact with this drug in a way that impacts the effect of this drug or causes a risk to the patient. Note: disease interactions are typically captured as contraindications.");
t("Another drug that is known to interact with this drug in a way that impacts the effect of this drug or causes a risk to the patient.");

// https://schema.org/interactionCount
t("interactionCount");
t("This property is deprecated, alongside the UserInteraction types on which it depended.");

// https://schema.org/interactionService
t("interactionService");
t("The WebSite or SoftwareApplication where the interactions took place.");

// https://schema.org/interactionStatistic
t("interactionStatistic");
t("The number of interactions for the CreativeWork using the WebSite or SoftwareApplication. The most specific child type of InteractionCounter should be used.");
t("The number of interactions for the CreativeWork using the WebSite or SoftwareApplication.");

// https://schema.org/interactionType
t("interactionType");
t('The Action representing the type of interaction. For up votes, +1s, etc. use <a class="localLink" href="/LikeAction">LikeAction</a>. For down votes use <a class="localLink" href="/DislikeAction">DislikeAction</a>. Otherwise, use the most specific Action.');
t("The Action representing the type of interaction.");

// https://schema.org/interactivityType
t("interactivityType");
t("The predominant mode of learning supported by the learning resource. Acceptable values are 'active', 'expositive', or 'mixed'.");
t("The predominant mode of learning supported by the learning resource.");

// https://schema.org/interestRate
t("interestRate");
t("The interest rate, charged or paid, applicable to the financial product. Note: This is different from the calculated annualPercentageRate.");
t("The interest rate, charged or paid, applicable to the financial product.");

// https://schema.org/interpretedAsClaim
t("interpretedAsClaim");
t('Used to indicate a specific claim contained, implied, translated or refined from the content of a <a class="localLink" href="/MediaObject">MediaObject</a> or other <a class="localLink" href="/CreativeWork">CreativeWork</a>. The interpreting party can be indicated using <a class="localLink" href="/claimInterpreter">claimInterpreter</a>.');
t('Used to indicate a specific claim contained, implied, translated or refined from the content of a <a class="localLink" href="/MediaObject">MediaObject</a> or other <a class="localLink" href="/CreativeWork">CreativeWork</a>.');

// https://schema.org/inventoryLevel
t("inventoryLevel");
t("The current approximate inventory level for the item or items.");

// https://schema.org/inverseOf
t("inverseOf");
t("Relates a property to a property that is its inverse. Inverse properties relate the same pairs of items to each other, but in reversed direction. For example, the 'alumni' and 'alumniOf' properties are inverseOf each other. Some properties don't have explicit inverses; in these situations RDFa and JSON-LD syntax for reverse properties can be used.");
t("Relates a property to a property that is its inverse.");

// https://schema.org/isAcceptingNewPatients
t("isAcceptingNewPatients");
t("Whether the provider is accepting new patients.");

// https://schema.org/isAccessibleForFree
t("isAccessibleForFree");
t("A flag to signal that the item, event, or place is accessible for free.");

// https://schema.org/isAccessoryOrSparePartFor
t("isAccessoryOrSparePartFor");
t("A pointer to another product (or multiple products) for which this product is an accessory or spare part.");

// https://schema.org/isAvailableGenerically
t("isAvailableGenerically");
t("True if the drug is available in a generic form (regardless of name).");

// https://schema.org/isBasedOn
t("isBasedOn");
t("A resource from which this work is derived or from which it is a modification or adaptation.");

// https://schema.org/isBasedOnUrl
t("isBasedOnUrl");
t("A resource that was used in the creation of this resource. This term can be repeated for multiple sources. For example, http://example.com/great-multiplication-intro.html.");
t("A resource that was used in the creation of this resource.");

// https://schema.org/isbn
t("isbn");
t("The ISBN of the book.");

// https://schema.org/isConsumableFor
t("isConsumableFor");
t("A pointer to another product (or multiple products) for which this product is a consumable.");

// https://schema.org/isEncodedByBioChemEntity
t("isEncodedByBioChemEntity");
t("Another BioChemEntity encoding by this one.");

// https://schema.org/isFamilyFriendly
t("isFamilyFriendly");
t("Indicates whether this content is family friendly.");

// https://schema.org/isGift
t("isGift");
t("Indicates whether the offer was accepted as a gift for someone other than the buyer.");

// https://schema.org/isicV4
t("isicV4");
t("The International Standard of Industrial Classification of All Economic Activities (ISIC), Revision 4 code for a particular organization, business person, or place.");

// https://schema.org/isInvolvedInBiologicalProcess
t("isInvolvedInBiologicalProcess");
t("Biological process this BioChemEntity is involved in; please use PropertyValue if you want to include any evidence.");

// https://schema.org/isLiveBroadcast
t("isLiveBroadcast");
t("True if the broadcast is of a live event.");

// https://schema.org/isLocatedInSubcellularLocation
t("isLocatedInSubcellularLocation");
t("Subcellular location where this BioChemEntity is located; please use PropertyValue if you want to include any evidence.");

// https://schema.org/iso6523Code
t("iso6523Code");
t('An organization identifier as defined in <a href="https://en.wikipedia.org/wiki/ISO/IEC_6523">ISO 6523(-1)</a>. The identifier should be in the <code>XXXX:YYYYYY:ZZZ</code> or <code>XXXX:YYYYYY</code>format. Where <code>XXXX</code> is a 4 digit <em>ICD</em> (International Code Designator), <code>YYYYYY</code> is an <em>OID</em> (Organization Identifier) with all formatting characters (dots, dashes, spaces) removed with a maximal length of 35 characters, and <code>ZZZ</code> is an optional OPI (Organization Part Identifier) with a maximum length of 35 characters. The various components (ICD, OID, OPI) are joined with a colon character (ASCII <code>0x3a</code>). Note that many existing organization identifiers defined as attributes like <a href="https://schema.org/leiCode">leiCode</a> (<code>0199</code>), <a href="https://schema.org/duns">duns</a> (<code>0060</code>) or <a href="https://schema.org/globalLocationNumber">GLN</a> (<code>0088</code>) can be expressed using ISO-6523. If possible, ISO-6523 codes should be preferred to populating <a href="https://schema.org/vatID">vatID</a> or <a href="https://schema.org/taxID">taxID</a>, as ISO identifiers are less ambiguous.');
t('An organization identifier as defined in <a href="https://en.wikipedia.org/wiki/ISO/IEC_6523">ISO 6523(-1)</a>.');

// https://schema.org/isPartOf
t("isPartOf");
t("Indicates an item or CreativeWork that this item, or CreativeWork (in some sense), is part of.");

// https://schema.org/isPartOfBioChemEntity
t("isPartOfBioChemEntity");
t("Indicates a BioChemEntity that is (in some sense) a part of this BioChemEntity.");

// https://schema.org/isPlanForApartment
t("isPlanForApartment");
t("Indicates some accommodation that this floor plan describes.");

// https://schema.org/isProprietary
t("isProprietary");
t("True if this item's name is a proprietary/brand name (vs. generic name).");

// https://schema.org/isrcCode
t("isrcCode");
t("The International Standard Recording Code for the recording.");

// https://schema.org/isRelatedTo
t("isRelatedTo");
t("A pointer to another, somehow related product (or multiple products).");

// https://schema.org/isResizable
t("isResizable");
t("Whether the 3DModel allows resizing. For example, room layout applications often do not allow 3DModel elements to be resized to reflect reality.");
t("Whether the 3DModel allows resizing.");

// https://schema.org/isSimilarTo
t("isSimilarTo");
t("A pointer to another, functionally similar product (or multiple products).");

// https://schema.org/issn
t("issn");
t("The International Standard Serial Number (ISSN) that identifies this serial publication. You can repeat this property to identify different formats of, or the linking ISSN (ISSN-L) for, this serial publication.");
t("The International Standard Serial Number (ISSN) that identifies this serial publication.");

// https://schema.org/issuedBy
t("issuedBy");
t('The organization issuing the item, for example a <a class="localLink" href="/Permit">Permit</a>, <a class="localLink" href="/Ticket">Ticket</a>, or <a class="localLink" href="/Certification">Certification</a>.');

// https://schema.org/issuedThrough
t("issuedThrough");
t("The service through which the permit was granted.");

// https://schema.org/issueNumber
t("issueNumber");
t('Identifies the issue of publication; for example, "iii" or "2".');

// https://schema.org/isUnlabelledFallback
t("isUnlabelledFallback");
t('This can be marked \'true\' to indicate that some published <a class="localLink" href="/DeliveryTimeSettings">DeliveryTimeSettings</a> or <a class="localLink" href="/ShippingRateSettings">ShippingRateSettings</a> are intended to apply to all <a class="localLink" href="/OfferShippingDetails">OfferShippingDetails</a> published by the same merchant, when referenced by a <a class="localLink" href="/shippingSettingsLink">shippingSettingsLink</a> in those settings. It is not meaningful to use a \'true\' value for this property alongside a transitTimeLabel (for <a class="localLink" href="/DeliveryTimeSettings">DeliveryTimeSettings</a>) or shippingLabel (for <a class="localLink" href="/ShippingRateSettings">ShippingRateSettings</a>), since this property is for use with unlabelled settings.');
t('This can be marked \'true\' to indicate that some published <a class="localLink" href="/DeliveryTimeSettings">DeliveryTimeSettings</a> or <a class="localLink" href="/ShippingRateSettings">ShippingRateSettings</a> are intended to apply to all <a class="localLink" href="/OfferShippingDetails">OfferShippingDetails</a> published by the same merchant, when referenced by a <a class="localLink" href="/shippingSettingsLink">shippingSettingsLink</a> in those settings.');

// https://schema.org/isVariantOf
t("isVariantOf");
t('Indicates the kind of product that this is a variant of. In the case of <a class="localLink" href="/ProductModel">ProductModel</a>, this is a pointer (from a ProductModel) to a base product from which this product is a variant. It is safe to infer that the variant inherits all product features from the base model, unless defined locally. This is not transitive. In the case of a <a class="localLink" href="/ProductGroup">ProductGroup</a>, the group description also serves as a template, representing a set of Products that vary on explicitly defined, specific dimensions only (so it defines both a set of variants, as well as which values distinguish amongst those variants). When used with <a class="localLink" href="/ProductGroup">ProductGroup</a>, this property can apply to any <a class="localLink" href="/Product">Product</a> included in the group.');
t("Indicates the kind of product that this is a variant of.");

// https://schema.org/iswcCode
t("iswcCode");
t("The International Standard Musical Work Code for the composition.");

// https://schema.org/item
t("item");
t("An entity represented by an entry in a list or data feed (e.g. an 'artist' in a list of 'artists').");

// https://schema.org/itemCondition
t("itemCondition");
t("A predefined value from OfferItemCondition specifying the condition of the product or service, or the products or services included in the offer. Also used for product return policies to specify the condition of products accepted for returns.");
t("A predefined value from OfferItemCondition specifying the condition of the product or service, or the products or services included in the offer.");

// https://schema.org/itemDefectReturnFees
t("itemDefectReturnFees");
t("The type of return fees for returns of defect products.");

// https://schema.org/itemDefectReturnLabelSource
t("itemDefectReturnLabelSource");
t("The method (from an enumeration) by which the customer obtains a return shipping label for a defect product.");

// https://schema.org/itemDefectReturnShippingFeesAmount
t("itemDefectReturnShippingFeesAmount");
t('Amount of shipping costs for defect product returns. Applicable when property <a class="localLink" href="/itemDefectReturnFees">itemDefectReturnFees</a> equals <a class="localLink" href="/ReturnShippingFees">ReturnShippingFees</a>.');
t("Amount of shipping costs for defect product returns.");

// https://schema.org/itemListElement
t("itemListElement");
t('For itemListElement values, you can use simple strings (e.g. "Peter", "Paul", "Mary"), existing entities, or use ListItem.<br/><br/>

Text values are best if the elements in the list are plain strings. Existing entities are best for a simple, unordered list of existing things in your data. ListItem is used with ordered lists when you want to provide additional context about the element in that list or when the same item might be in different places in different lists.<br/><br/>

Note: The order of elements in your mark-up is not sufficient for indicating the order or elements.  Use ListItem with a \'position\' property in such cases.');
t('For itemListElement values, you can use simple strings (e.g. "Peter", "Paul", "Mary"), existing entities, or use ListItem.');

// https://schema.org/itemListOrder
t("itemListOrder");
t("Type of ordering (e.g. Ascending, Descending, Unordered).");

// https://schema.org/itemLocation
t("itemLocation");
t("Current location of the item.");

// https://schema.org/itemOffered
t("itemOffered");
t('An item being offered (or demanded). The transactional nature of the offer or demand is documented using <a class="localLink" href="/businessFunction">businessFunction</a>, e.g. sell, lease etc. While several common expected types are listed explicitly in this definition, others can be used. Using a second type, such as Product or a subtype of Product, can clarify the nature of the offer.');
t("An item being offered (or demanded).");

// https://schema.org/itemReviewed
t("itemReviewed");
t("The item that is being reviewed/rated.");

// https://schema.org/itemShipped
t("itemShipped");
t("Item(s) being shipped.");

// https://schema.org/itinerary
t("itinerary");
t('Destination(s) ( <a class="localLink" href="/Place">Place</a> ) that make up a trip. For a trip where destination order is important use <a class="localLink" href="/ItemList">ItemList</a> to specify that order (see examples).');
t('Destination(s) ( <a class="localLink" href="/Place">Place</a> ) that make up a trip.');

// https://schema.org/iupacName
t("iupacName");
t("Systematic method of naming chemical compounds as recommended by the International Union of Pure and Applied Chemistry (IUPAC).");

// https://schema.org/jobBenefits
t("jobBenefits");
t("Description of benefits associated with the job.");

// https://schema.org/jobImmediateStart
t("jobImmediateStart");
t("An indicator as to whether a position is available for an immediate start.");

// https://schema.org/jobLocation
t("jobLocation");
t("A (typically single) geographic location associated with the job position.");

// https://schema.org/jobLocationType
t("jobLocationType");
t("A description of the job location (e.g. TELECOMMUTE for telecommute jobs).");

// https://schema.org/jobStartDate
t("jobStartDate");
t("The date on which a successful applicant for this job would be expected to start work. Choose a specific date in the future or use the jobImmediateStart property to indicate the position is to be filled as soon as possible.");
t("The date on which a successful applicant for this job would be expected to start work.");

// https://schema.org/jobTitle
t("jobTitle");
t("The job title of the person (for example, Financial Manager).");

// https://schema.org/jurisdiction
t("jurisdiction");
t("Indicates a legal jurisdiction, e.g. of some legislation, or where some government service is based.");

// https://schema.org/keywords
t("keywords");
t("Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.");
t("Keywords or tags used to describe some item.");

// https://schema.org/knownVehicleDamages
t("knownVehicleDamages");
t("A textual description of known damages, both repaired and unrepaired.");

// https://schema.org/knows
t("knows");
t("The most generic bi-directional social/work relation.");

// https://schema.org/knowsAbout
t("knowsAbout");
t('Of a <a class="localLink" href="/Person">Person</a>, and less typically of an <a class="localLink" href="/Organization">Organization</a>, to indicate a topic that is known about - suggesting possible expertise but not implying it. We do not distinguish skill levels here, or relate this to educational content, events, objectives or <a class="localLink" href="/JobPosting">JobPosting</a> descriptions.');
t('Of a <a class="localLink" href="/Person">Person</a>, and less typically of an <a class="localLink" href="/Organization">Organization</a>, to indicate a topic that is known about - suggesting possible expertise but not implying it.');

// https://schema.org/knowsLanguage
t("knowsLanguage");
t('Of a <a class="localLink" href="/Person">Person</a>, and less typically of an <a class="localLink" href="/Organization">Organization</a>, to indicate a known language. We do not distinguish skill levels or reading/writing/speaking/signing here. Use language codes from the <a href="http://tools.ietf.org/html/bcp47">IETF BCP 47 standard</a>.');
t('Of a <a class="localLink" href="/Person">Person</a>, and less typically of an <a class="localLink" href="/Organization">Organization</a>, to indicate a known language.');

// https://schema.org/labelDetails
t("labelDetails");
t("Link to the drug's label details.");

// https://schema.org/landlord
t("landlord");
t("A sub property of participant. The owner of the real estate property.");
t("A sub property of participant.");

// https://schema.org/language
t("language");
t("A sub property of instrument. The language used on this action.");
t("A sub property of instrument.");

// https://schema.org/lastReviewed
t("lastReviewed");
t("Date on which the content on this web page was last reviewed for accuracy and/or completeness.");

// https://schema.org/latitude
t("latitude");
t('The latitude of a location. For example <code>37.42242</code> (<a href="https://en.wikipedia.org/wiki/World_Geodetic_System">WGS 84</a>).');
t("The latitude of a location.");

// https://schema.org/layoutImage
t("layoutImage");
t("A schematic image showing the floorplan layout.");

// https://schema.org/learningResourceType
t("learningResourceType");
t("The predominant type or kind characterizing the learning resource. For example, 'presentation', 'handout'.");
t("The predominant type or kind characterizing the learning resource.");

// https://schema.org/leaseLength
t("leaseLength");
t('Length of the lease for some <a class="localLink" href="/Accommodation">Accommodation</a>, either particular to some <a class="localLink" href="/Offer">Offer</a> or in some cases intrinsic to the property.');

// https://schema.org/legalName
t("legalName");
t("The official name of the organization, e.g. the registered company name.");

// https://schema.org/legalStatus
t("legalStatus");
t("The drug or supplement's legal status, including any controlled substance schedules that apply.");

// https://schema.org/legislationApplies
t("legislationApplies");
t('Indicates that this legislation (or part of a legislation) somehow transfers another legislation in a different legislative context. This is an informative link, and it has no legal value. For legally-binding links of transposition, use the <a href="/legislationTransposes">legislationTransposes</a> property. For example an informative consolidated law of a European Union\'s member state "applies" the consolidated version of the European Directive implemented in it.');
t("Indicates that this legislation (or part of a legislation) somehow transfers another legislation in a different legislative context.");

// https://schema.org/legislationChanges
t("legislationChanges");
t('Another legislation that this legislation changes. This encompasses the notions of amendment, replacement, correction, repeal, or other types of change. This may be a direct change (textual or non-textual amendment) or a consequential or indirect change. The property is to be used to express the existence of a change relationship between two acts rather than the existence of a consolidated version of the text that shows the result of the change. For consolidation relationships, use the <a href="/legislationConsolidates">legislationConsolidates</a> property.');
t("Another legislation that this legislation changes.");

// https://schema.org/legislationConsolidates
t("legislationConsolidates");
t("Indicates another legislation taken into account in this consolidated legislation (which is usually the product of an editorial process that revises the legislation). This property should be used multiple times to refer to both the original version or the previous consolidated version, and to the legislations making the change.");
t("Indicates another legislation taken into account in this consolidated legislation (which is usually the product of an editorial process that revises the legislation).");

// https://schema.org/legislationDate
t("legislationDate");
t("The date of adoption or signature of the legislation. This is the date at which the text is officially aknowledged to be a legislation, even though it might not even be published or in force.");
t("The date of adoption or signature of the legislation.");

// https://schema.org/legislationDateVersion
t("legislationDateVersion");
t('The point-in-time at which the provided description of the legislation is valid (e.g.: when looking at the law on the 2016-04-07 (= dateVersion), I get the consolidation of 2015-04-12 of the "National Insurance Contributions Act 2015")');

// https://schema.org/legislationIdentifier
t("legislationIdentifier");
t("An identifier for the legislation. This can be either a string-based identifier, like the CELEX at EU level or the NOR in France, or a web-based, URL/URI identifier, like an ELI (European Legislation Identifier) or an URN-Lex.");
t("An identifier for the legislation.");

// https://schema.org/legislationJurisdiction
t("legislationJurisdiction");
t("The jurisdiction from which the legislation originates.");

// https://schema.org/legislationLegalForce
t("legislationLegalForce");
t("Whether the legislation is currently in force, not in force, or partially in force.");

// https://schema.org/legislationLegalValue
t("legislationLegalValue");
t('The legal value of this legislation file. The same legislation can be written in multiple files with different legal values. Typically a digitally signed PDF have a "stronger" legal value than the HTML file of the same act.');
t("The legal value of this legislation file.");

// https://schema.org/legislationPassedBy
t("legislationPassedBy");
t('The person or organization that originally passed or made the law: typically parliament (for primary legislation) or government (for secondary legislation). This indicates the "legal author" of the law, as opposed to its physical author.');
t("The person or organization that originally passed or made the law: typically parliament (for primary legislation) or government (for secondary legislation).");

// https://schema.org/legislationResponsible
t("legislationResponsible");
t("An individual or organization that has some kind of responsibility for the legislation. Typically the ministry who is/was in charge of elaborating the legislation, or the adressee for potential questions about the legislation once it is published.");
t("An individual or organization that has some kind of responsibility for the legislation.");

// https://schema.org/legislationTransposes
t("legislationTransposes");
t("Indicates that this legislation (or part of legislation) fulfills the objectives set by another legislation, by passing appropriate implementation measures. Typically, some legislations of European Union's member states or regions transpose European Directives. This indicates a legally binding link between the 2 legislations.");
t("Indicates that this legislation (or part of legislation) fulfills the objectives set by another legislation, by passing appropriate implementation measures.");

// https://schema.org/legislationType
t("legislationType");
t('The type of the legislation. Examples of values are "law", "act", "directive", "decree", "regulation", "statutory instrument", "loi organique", "règlement grand-ducal", etc., depending on the country.');
t("The type of the legislation.");

// https://schema.org/leiCode
t("leiCode");
t("An organization identifier that uniquely identifies a legal entity as defined in ISO 17442.");

// https://schema.org/lender
t("lender");
t("A sub property of participant. The person that lends the object being borrowed.");
t("A sub property of participant.");

// https://schema.org/lesser
t("lesser");
t("This ordering relation for qualitative values indicates that the subject is lesser than the object.");

// https://schema.org/lesserOrEqual
t("lesserOrEqual");
t("This ordering relation for qualitative values indicates that the subject is lesser than or equal to the object.");

// https://schema.org/letterer
t("letterer");
t("The individual who adds lettering, including speech balloons and sound effects, to artwork.");

// https://schema.org/license
t("license");
t("A license document that applies to this content, typically indicated by URL.");

// https://schema.org/line
t("line");
t("A line is a point-to-point path consisting of two or more points. A line is expressed as a series of two or more point objects separated by space.");
t("A line is a point-to-point path consisting of two or more points.");

// https://schema.org/linkRelationship
t("linkRelationship");
t("Indicates the relationship type of a Web link.");

// https://schema.org/liveBlogUpdate
t("liveBlogUpdate");
t("An update to the LiveBlog.");

// https://schema.org/loanMortgageMandateAmount
t("loanMortgageMandateAmount");
t("Amount of mortgage mandate that can be converted into a proper mortgage at a later stage.");

// https://schema.org/loanPaymentAmount
t("loanPaymentAmount");
t("The amount of money to pay in a single payment.");

// https://schema.org/loanPaymentFrequency
t("loanPaymentFrequency");
t("Frequency of payments due, i.e. number of months between payments. This is defined as a frequency, i.e. the reciprocal of a period of time.");
t("Frequency of payments due, i.e. number of months between payments.");

// https://schema.org/loanRepaymentForm
t("loanRepaymentForm");
t("A form of paying back money previously borrowed from a lender. Repayment usually takes the form of periodic payments that normally include part principal plus interest in each payment.");
t("A form of paying back money previously borrowed from a lender.");

// https://schema.org/loanTerm
t("loanTerm");
t("The duration of the loan or credit agreement.");

// https://schema.org/loanType
t("loanType");
t("The type of a loan or credit.");

// https://schema.org/location
t("location");
t("The location of, for example, where an event is happening, where an organization is located, or where an action takes place.");

// https://schema.org/locationCreated
t("locationCreated");
t("The location where the CreativeWork was created, which may not be the same as the location depicted in the CreativeWork.");

// https://schema.org/lodgingUnitDescription
t("lodgingUnitDescription");
t("A full description of the lodging unit.");

// https://schema.org/lodgingUnitType
t("lodgingUnitType");
t("Textual description of the unit type (including suite vs. room, size of bed, etc.).");

// https://schema.org/logo
t("logo");
t("An associated logo.");

// https://schema.org/longitude
t("longitude");
t('The longitude of a location. For example <code>-122.08585</code> (<a href="https://en.wikipedia.org/wiki/World_Geodetic_System">WGS 84</a>).');
t("The longitude of a location.");

// https://schema.org/loser
t("loser");
t("A sub property of participant. The loser of the action.");
t("A sub property of participant.");

// https://schema.org/lowPrice
t("lowPrice");
t("The lowest price of all offers available.<br/><br/>

Usage guidelines:<br/><br/>

<ul>
<li>Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.</li>
<li>Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.</li>
</ul>");
t("The lowest price of all offers available.");

// https://schema.org/lyricist
t("lyricist");
t("The person who wrote the words.");

// https://schema.org/lyrics
t("lyrics");
t("The words in the song.");

// https://schema.org/mainContentOfPage
t("mainContentOfPage");
t("Indicates if this web page element is the main subject of the page.");

// https://schema.org/mainEntity
t("mainEntity");
t("Indicates the primary entity described in some page or other CreativeWork.");

// https://schema.org/mainEntityOfPage
t("mainEntityOfPage");
t('Indicates a page (or other CreativeWork) for which this thing is the main entity being described. See <a href="/docs/datamodel.html#mainEntityBackground">background notes</a> for details.');
t("Indicates a page (or other CreativeWork) for which this thing is the main entity being described.");

// https://schema.org/maintainer
t("maintainer");
t('A maintainer of a <a class="localLink" href="/Dataset">Dataset</a>, software package (<a class="localLink" href="/SoftwareApplication">SoftwareApplication</a>), or other <a class="localLink" href="/Project">Project</a>. A maintainer is a <a class="localLink" href="/Person">Person</a> or <a class="localLink" href="/Organization">Organization</a> that manages contributions to, and/or publication of, some (typically complex) artifact. It is common for distributions of software and data to be based on "upstream" sources. When <a class="localLink" href="/maintainer">maintainer</a> is applied to a specific version of something e.g. a particular version or packaging of a <a class="localLink" href="/Dataset">Dataset</a>, it is always  possible that the upstream source has a different maintainer. The <a class="localLink" href="/isBasedOn">isBasedOn</a> property can be used to indicate such relationships between datasets to make the different maintenance roles clear. Similarly in the case of software, a package may have dedicated maintainers working on integration into software distributions such as Ubuntu, as well as upstream maintainers of the underlying work.');
t('A maintainer of a <a class="localLink" href="/Dataset">Dataset</a>, software package (<a class="localLink" href="/SoftwareApplication">SoftwareApplication</a>), or other <a class="localLink" href="/Project">Project</a>.');

// https://schema.org/makesOffer
t("makesOffer");
t("A pointer to products or services offered by the organization or person.");

// https://schema.org/manufacturer
t("manufacturer");
t("The manufacturer of the product.");

// https://schema.org/map
t("map");
t("A URL to a map of the place.");

// https://schema.org/maps
t("maps");
t("A URL to a map of the place.");

// https://schema.org/mapType
t("mapType");
t("Indicates the kind of Map, from the MapCategoryType Enumeration.");

// https://schema.org/marginOfError
t("marginOfError");
t('A <a class="localLink" href="/marginOfError">marginOfError</a> for an <a class="localLink" href="/Observation">Observation</a>.');

// https://schema.org/masthead
t("masthead");
t('For a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>, a link to the masthead page or a page listing top editorial management.');

// https://schema.org/material
t("material");
t("A material that something is made from, e.g. leather, wool, cotton, paper.");

// https://schema.org/materialExtent
t("materialExtent");
t("The quantity of the materials being described or an expression of the physical space they occupy.");

// https://schema.org/mathExpression
t("mathExpression");
t("A mathematical expression (e.g. 'x^2-3x=0') that may be solved for a specific variable, simplified, or transformed. This can take many formats, e.g. LaTeX, Ascii-Math, or math as you would write with a keyboard.");
t("A mathematical expression (e.g. 'x^2-3x=0') that may be solved for a specific variable, simplified, or transformed.");

// https://schema.org/maximumAttendeeCapacity
t("maximumAttendeeCapacity");
t("The total number of individuals that may attend an event or venue.");

// https://schema.org/maximumEnrollment
t("maximumEnrollment");
t("The maximum number of students who may be enrolled in the program.");

// https://schema.org/maximumIntake
t("maximumIntake");
t("Recommended intake of this supplement for a given population as defined by a specific recommending authority.");

// https://schema.org/maximumPhysicalAttendeeCapacity
t("maximumPhysicalAttendeeCapacity");
t('The maximum physical attendee capacity of an <a class="localLink" href="/Event">Event</a> whose <a class="localLink" href="/eventAttendanceMode">eventAttendanceMode</a> is <a class="localLink" href="/OfflineEventAttendanceMode">OfflineEventAttendanceMode</a> (or the offline aspects, in the case of a <a class="localLink" href="/MixedEventAttendanceMode">MixedEventAttendanceMode</a>).');

// https://schema.org/maximumVirtualAttendeeCapacity
t("maximumVirtualAttendeeCapacity");
t('The maximum virtual attendee capacity of an <a class="localLink" href="/Event">Event</a> whose <a class="localLink" href="/eventAttendanceMode">eventAttendanceMode</a> is <a class="localLink" href="/OnlineEventAttendanceMode">OnlineEventAttendanceMode</a> (or the online aspects, in the case of a <a class="localLink" href="/MixedEventAttendanceMode">MixedEventAttendanceMode</a>).');

// https://schema.org/maxPrice
t("maxPrice");
t("The highest price if the price is a range.");

// https://schema.org/maxValue
t("maxValue");
t("The upper value of some characteristic or property.");

// https://schema.org/mealService
t("mealService");
t("Description of the meals that will be provided or available for purchase.");

// https://schema.org/measuredProperty
t("measuredProperty");
t('The measuredProperty of an <a class="localLink" href="/Observation">Observation</a>, typically via its <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>. There are various kinds of applicable <a class="localLink" href="/Property">Property</a>: a schema.org property, a property from other RDF-compatible systems, e.g. W3C RDF Data Cube, Data Commons, Wikidata, or schema.org extensions such as <a href="https://www.gs1.org/voc/?show=properties">GS1\'s</a>.');
t('The measuredProperty of an <a class="localLink" href="/Observation">Observation</a>, typically via its <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>.');

// https://schema.org/measurementDenominator
t("measurementDenominator");
t("Identifies the denominator variable when an observation represents a ratio or percentage.");

// https://schema.org/measurementMethod
t("measurementMethod");
t('A subproperty of <a class="localLink" href="/measurementTechnique">measurementTechnique</a> that can be used for specifying specific methods, in particular via <a class="localLink" href="/MeasurementMethodEnum">MeasurementMethodEnum</a>.');

// https://schema.org/measurementQualifier
t("measurementQualifier");
t("Provides additional qualification to an observation. For example, a GDP observation measures the Nominal value.");
t("Provides additional qualification to an observation.");

// https://schema.org/measurementTechnique
t("measurementTechnique");
t('A technique, method or technology used in an <a class="localLink" href="/Observation">Observation</a>, <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> or <a class="localLink" href="/Dataset">Dataset</a> (or <a class="localLink" href="/DataDownload">DataDownload</a>, <a class="localLink" href="/DataCatalog">DataCatalog</a>), corresponding to the method used for measuring the corresponding variable(s) (for datasets, described using <a class="localLink" href="/variableMeasured">variableMeasured</a>; for <a class="localLink" href="/Observation">Observation</a>, a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>). Often but not necessarily each <a class="localLink" href="/variableMeasured">variableMeasured</a> will have an explicit representation as (or mapping to) an property such as those defined in Schema.org, or other RDF vocabularies and "knowledge graphs". In that case the subproperty of <a class="localLink" href="/variableMeasured">variableMeasured</a> called <a class="localLink" href="/measuredProperty">measuredProperty</a> is applicable.<br/><br/>

The <a class="localLink" href="/measurementTechnique">measurementTechnique</a> property helps when extra clarification is needed about how a <a class="localLink" href="/measuredProperty">measuredProperty</a> was measured. This is oriented towards scientific and scholarly dataset publication but may have broader applicability; it is not intended as a full representation of measurement, but can often serve as a high level summary for dataset discovery. <br/><br/>

For example, if <a class="localLink" href="/variableMeasured">variableMeasured</a> is: molecule concentration, <a class="localLink" href="/measurementTechnique">measurementTechnique</a> could be: "mass spectrometry" or "nmr spectroscopy" or "colorimetry" or "immunofluorescence". If the <a class="localLink" href="/variableMeasured">variableMeasured</a> is "depression rating", the <a class="localLink" href="/measurementTechnique">measurementTechnique</a> could be "Zung Scale" or "HAM-D" or "Beck Depression Inventory". <br/><br/>

If there are several <a class="localLink" href="/variableMeasured">variableMeasured</a> properties recorded for some given data object, use a <a class="localLink" href="/PropertyValue">PropertyValue</a> for each <a class="localLink" href="/variableMeasured">variableMeasured</a> and attach the corresponding <a class="localLink" href="/measurementTechnique">measurementTechnique</a>. The value can also be from an enumeration, organized as a <a class="localLink" href="/MeasurementMetholdEnumeration">MeasurementMetholdEnumeration</a>.');
t('A technique, method or technology used in an <a class="localLink" href="/Observation">Observation</a>, <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> or <a class="localLink" href="/Dataset">Dataset</a> (or <a class="localLink" href="/DataDownload">DataDownload</a>, <a class="localLink" href="/DataCatalog">DataCatalog</a>), corresponding to the method used for measuring the corresponding variable(s) (for datasets, described using <a class="localLink" href="/variableMeasured">variableMeasured</a>; for <a class="localLink" href="/Observation">Observation</a>, a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>).');

// https://schema.org/mechanismOfAction
t("mechanismOfAction");
t("The specific biochemical interaction through which this drug or supplement produces its pharmacological effect.");

// https://schema.org/mediaAuthenticityCategory
t("mediaAuthenticityCategory");
t("Indicates a MediaManipulationRatingEnumeration classification of a media object (in the context of how it was published or shared).");

// https://schema.org/mediaItemAppearance
t("mediaItemAppearance");
t('In the context of a <a class="localLink" href="/MediaReview">MediaReview</a>, indicates specific media item(s) that are grouped using a <a class="localLink" href="/MediaReviewItem">MediaReviewItem</a>.');

// https://schema.org/median
t("median");
t("The median value.");

// https://schema.org/medicalAudience
t("medicalAudience");
t("Medical audience for page.");

// https://schema.org/medicalSpecialty
t("medicalSpecialty");
t("A medical specialty of the provider.");

// https://schema.org/medicineSystem
t("medicineSystem");
t("The system of medicine that includes this MedicalEntity, for example 'evidence-based', 'homeopathic', 'chiropractic', etc.");

// https://schema.org/meetsEmissionStandard
t("meetsEmissionStandard");
t("Indicates that the vehicle meets the respective emission standard.");

// https://schema.org/member
t("member");
t("A member of an Organization or a ProgramMembership. Organizations can be members of organizations; ProgramMembership is typically for individuals.");
t("A member of an Organization or a ProgramMembership.");

// https://schema.org/memberOf
t("memberOf");
t("An Organization (or ProgramMembership) to which this Person or Organization belongs.");

// https://schema.org/members
t("members");
t("A member of this organization.");

// https://schema.org/membershipNumber
t("membershipNumber");
t("A unique identifier for the membership.");

// https://schema.org/membershipPointsEarned
t("membershipPointsEarned");
t("The number of membership points earned by the member. If necessary, the unitText can be used to express the units the points are issued in. (E.g. stars, miles, etc.)");
t("The number of membership points earned by the member.");

// https://schema.org/memoryRequirements
t("memoryRequirements");
t("Minimum memory requirements.");

// https://schema.org/mentions
t("mentions");
t("Indicates that the CreativeWork contains a reference to, but is not necessarily about a concept.");

// https://schema.org/menu
t("menu");
t("Either the actual menu as a structured representation, as text, or a URL of the menu.");

// https://schema.org/menuAddOn
t("menuAddOn");
t("Additional menu item(s) such as a side dish of salad or side order of fries that can be added to this menu item. Additionally it can be a menu section containing allowed add-on menu items for this menu item.");
t("Additional menu item(s) such as a side dish of salad or side order of fries that can be added to this menu item.");

// https://schema.org/merchant
t("merchant");
t("'merchant' is an out-dated term for 'seller'.");

// https://schema.org/merchantReturnDays
t("merchantReturnDays");
t('Specifies either a fixed return date or the number of days (from the delivery date) that a product can be returned. Used when the <a class="localLink" href="/returnPolicyCategory">returnPolicyCategory</a> property is specified as <a class="localLink" href="/MerchantReturnFiniteReturnWindow">MerchantReturnFiniteReturnWindow</a>.');
t("Specifies either a fixed return date or the number of days (from the delivery date) that a product can be returned.");

// https://schema.org/merchantReturnLink
t("merchantReturnLink");
t("Specifies a Web page or service by URL, for product returns.");

// https://schema.org/messageAttachment
t("messageAttachment");
t("A CreativeWork attached to the message.");

// https://schema.org/mileageFromOdometer
t("mileageFromOdometer");
t("The total distance travelled by the particular vehicle since its initial production, as read from its odometer.<br/><br/>

Typical unit code(s): KMT for kilometers, SMI for statute miles.");
t("The total distance travelled by the particular vehicle since its initial production, as read from its odometer.");

// https://schema.org/minimumPaymentDue
t("minimumPaymentDue");
t("The minimum payment required at this time.");

// https://schema.org/minPrice
t("minPrice");
t("The lowest price if the price is a range.");

// https://schema.org/minValue
t("minValue");
t("The lower value of some characteristic or property.");

// https://schema.org/missionCoveragePrioritiesPolicy
t("missionCoveragePrioritiesPolicy");
t('For a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>, a statement on coverage priorities, including any public agenda or stance on issues.');

// https://schema.org/mobileUrl
t("mobileUrl");
t('The <a class="localLink" href="/mobileUrl">mobileUrl</a> property is provided for specific situations in which data consumers need to determine whether one of several provided URLs is a dedicated \'mobile site\'.<br/><br/>

To discourage over-use, and reflecting intial usecases, the property is expected only on <a class="localLink" href="/Product">Product</a> and <a class="localLink" href="/Offer">Offer</a>, rather than <a class="localLink" href="/Thing">Thing</a>. The general trend in web technology is towards <a href="https://en.wikipedia.org/wiki/Responsive_web_design">responsive design</a> in which content can be flexibly adapted to a wide range of browsing environments. Pages and sites referenced with the long-established <a class="localLink" href="/url">url</a> property should ideally also be usable on a wide variety of devices, including mobile phones. In most cases, it would be pointless and counter productive to attempt to update all <a class="localLink" href="/url">url</a> markup to use <a class="localLink" href="/mobileUrl">mobileUrl</a> for more mobile-oriented pages. The property is intended for the case when items (primarily <a class="localLink" href="/Product">Product</a> and <a class="localLink" href="/Offer">Offer</a>) have extra URLs hosted on an additional "mobile site" alongside the main one. It should not be taken as an endorsement of this publication style.');
t('The <a class="localLink" href="/mobileUrl">mobileUrl</a> property is provided for specific situations in which data consumers need to determine whether one of several provided URLs is a dedicated \'mobile site\'.');

// https://schema.org/model
t("model");
t("The model of the product. Use with the URL of a ProductModel or a textual representation of the model identifier. The URL of the ProductModel can be from an external source. It is recommended to additionally provide strong product identifiers via the gtin8/gtin13/gtin14 and mpn properties.");
t("The model of the product.");

// https://schema.org/modelDate
t("modelDate");
t("The release date of a vehicle model (often used to differentiate versions of the same make and model).");

// https://schema.org/modifiedTime
t("modifiedTime");
t("The date and time the reservation was modified.");

// https://schema.org/molecularFormula
t("molecularFormula");
t("The empirical formula is the simplest whole number ratio of all the atoms in a molecule.");

// https://schema.org/molecularWeight
t("molecularWeight");
t("This is the molecular weight of the entity being described, not of the parent. Units should be included in the form '&lt;Number&gt; &lt;unit&gt;', for example '12 amu' or as '&lt;QuantitativeValue&gt;.");
t("This is the molecular weight of the entity being described, not of the parent.");

// https://schema.org/monoisotopicMolecularWeight
t("monoisotopicMolecularWeight");
t("The monoisotopic mass is the sum of the masses of the atoms in a molecule using the unbound, ground-state, rest mass of the principal (most abundant) isotope for each element instead of the isotopic average mass. Please include the units in the form '&lt;Number&gt; &lt;unit&gt;', for example '770.230488 g/mol' or as '&lt;QuantitativeValue&gt;.");
t("The monoisotopic mass is the sum of the masses of the atoms in a molecule using the unbound, ground-state, rest mass of the principal (most abundant) isotope for each element instead of the isotopic average mass.");

// https://schema.org/monthlyMinimumRepaymentAmount
t("monthlyMinimumRepaymentAmount");
t("The minimum payment is the lowest amount of money that one is required to pay on a credit card statement each month.");

// https://schema.org/monthsOfExperience
t("monthsOfExperience");
t("Indicates the minimal number of months of experience required for a position.");

// https://schema.org/mpn
t("mpn");
t("The Manufacturer Part Number (MPN) of the product, or the product to which the offer refers.");

// https://schema.org/multipleValues
t("multipleValues");
t("Whether multiple values are allowed for the property.  Default is false.");
t("Whether multiple values are allowed for the property.");

// https://schema.org/muscleAction
t("muscleAction");
t("The movement the muscle generates.");

// https://schema.org/musicalKey
t("musicalKey");
t("The key, mode, or scale this composition uses.");

// https://schema.org/musicArrangement
t("musicArrangement");
t("An arrangement derived from the composition.");

// https://schema.org/musicBy
t("musicBy");
t("The composer of the soundtrack.");

// https://schema.org/musicCompositionForm
t("musicCompositionForm");
t("The type of composition (e.g. overture, sonata, symphony, etc.).");

// https://schema.org/musicGroupMember
t("musicGroupMember");
t("A member of a music group&#x2014;for example, John, Paul, George, or Ringo.");

// https://schema.org/musicReleaseFormat
t("musicReleaseFormat");
t("Format of this release (the type of recording media used, i.e. compact disc, digital media, LP, etc.).");

// https://schema.org/naics
t("naics");
t("The North American Industry Classification System (NAICS) code for a particular organization or business person.");

// https://schema.org/name
t("name");
t("The name of the item.");

// https://schema.org/namedPosition
t("namedPosition");
t("A position played, performed or filled by a person or organization, as part of an organization. For example, an athlete in a SportsTeam might play in the position named 'Quarterback'.");
t("A position played, performed or filled by a person or organization, as part of an organization.");

// https://schema.org/nationality
t("nationality");
t("Nationality of the person.");

// https://schema.org/naturalProgression
t("naturalProgression");
t("The expected progression of the condition if it is not treated and allowed to progress naturally.");

// https://schema.org/negativeNotes
t("negativeNotes");
t('Provides negative considerations regarding something, most typically in pro/con lists for reviews (alongside <a class="localLink" href="/positiveNotes">positiveNotes</a>). For symmetry <br/><br/>

In the case of a <a class="localLink" href="/Review">Review</a>, the property describes the <a class="localLink" href="/itemReviewed">itemReviewed</a> from the perspective of the review; in the case of a <a class="localLink" href="/Product">Product</a>, the product itself is being described. Since product descriptions
tend to emphasise positive claims, it may be relatively unusual to find <a class="localLink" href="/negativeNotes">negativeNotes</a> used in this way. Nevertheless for the sake of symmetry, <a class="localLink" href="/negativeNotes">negativeNotes</a> can be used on <a class="localLink" href="/Product">Product</a>.<br/><br/>

The property values can be expressed either as unstructured text (repeated as necessary), or if ordered, as a list (in which case the most negative is at the beginning of the list).');
t('Provides negative considerations regarding something, most typically in pro/con lists for reviews (alongside <a class="localLink" href="/positiveNotes">positiveNotes</a>).');

// https://schema.org/nerve
t("nerve");
t("The underlying innervation associated with the muscle.");

// https://schema.org/nerveMotor
t("nerveMotor");
t("The neurological pathway extension that involves muscle control.");

// https://schema.org/netWorth
t("netWorth");
t("The total financial value of the person as calculated by subtracting assets from liabilities.");

// https://schema.org/newsUpdatesAndGuidelines
t("newsUpdatesAndGuidelines");
t('Indicates a page with news updates and guidelines. This could often be (but is not required to be) the main page containing <a class="localLink" href="/SpecialAnnouncement">SpecialAnnouncement</a> markup on a site.');
t("Indicates a page with news updates and guidelines.");

// https://schema.org/nextItem
t("nextItem");
t("A link to the ListItem that follows the current one.");

// https://schema.org/noBylinesPolicy
t("noBylinesPolicy");
t('For a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a> or other news-related <a class="localLink" href="/Organization">Organization</a>, a statement explaining when authors of articles are not named in bylines.');

// https://schema.org/nonEqual
t("nonEqual");
t("This ordering relation for qualitative values indicates that the subject is not equal to the object.");

// https://schema.org/nonprofitStatus
t("nonprofitStatus");
t("nonprofitStatus indicates the legal status of a non-profit organization in its primary place of business.");

// https://schema.org/nonProprietaryName
t("nonProprietaryName");
t("The generic name of this drug or supplement.");

// https://schema.org/normalRange
t("normalRange");
t("Range of acceptable values for a typical patient, when applicable.");

// https://schema.org/nsn
t("nsn");
t('Indicates the <a href="https://en.wikipedia.org/wiki/NATO_Stock_Number">NATO stock number</a> (nsn) of a <a class="localLink" href="/Product">Product</a>.');

// https://schema.org/numAdults
t("numAdults");
t("The number of adults staying in the unit.");

// https://schema.org/numberedPosition
t("numberedPosition");
t("A number associated with a role in an organization, for example, the number on an athlete's jersey.");

// https://schema.org/numberOfAccommodationUnits
t("numberOfAccommodationUnits");
t('Indicates the total (available plus unavailable) number of accommodation units in an <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a>, or the number of accommodation units for a specific <a class="localLink" href="/FloorPlan">FloorPlan</a> (within its specific <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a>). See also <a class="localLink" href="/numberOfAvailableAccommodationUnits">numberOfAvailableAccommodationUnits</a>.');
t('Indicates the total (available plus unavailable) number of accommodation units in an <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a>, or the number of accommodation units for a specific <a class="localLink" href="/FloorPlan">FloorPlan</a> (within its specific <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a>).');

// https://schema.org/numberOfAirbags
t("numberOfAirbags");
t("The number or type of airbags in the vehicle.");

// https://schema.org/numberOfAvailableAccommodationUnits
t("numberOfAvailableAccommodationUnits");
t('Indicates the number of available accommodation units in an <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a>, or the number of accommodation units for a specific <a class="localLink" href="/FloorPlan">FloorPlan</a> (within its specific <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a>). See also <a class="localLink" href="/numberOfAccommodationUnits">numberOfAccommodationUnits</a>.');
t('Indicates the number of available accommodation units in an <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a>, or the number of accommodation units for a specific <a class="localLink" href="/FloorPlan">FloorPlan</a> (within its specific <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a>).');

// https://schema.org/numberOfAxles
t("numberOfAxles");
t("The number of axles.<br/><br/>

Typical unit code(s): C62.");
t("The number of axles.");

// https://schema.org/numberOfBathroomsTotal
t("numberOfBathroomsTotal");
t('The total integer number of bathrooms in some <a class="localLink" href="/Accommodation">Accommodation</a>, following real estate conventions as <a href="https://ddwiki.reso.org/display/DDW17/BathroomsTotalInteger+Field">documented in RESO</a>: "The simple sum of the number of bathrooms. For example for a property with two Full Bathrooms and one Half Bathroom, the Bathrooms Total Integer will be 3.". See also <a class="localLink" href="/numberOfRooms">numberOfRooms</a>.');
t('The total integer number of bathrooms in some <a class="localLink" href="/Accommodation">Accommodation</a>, following real estate conventions as <a href="https://ddwiki.reso.org/display/DDW17/BathroomsTotalInteger+Field">documented in RESO</a>: "The simple sum of the number of bathrooms.');

// https://schema.org/numberOfBedrooms
t("numberOfBedrooms");
t('The total integer number of bedrooms in a some <a class="localLink" href="/Accommodation">Accommodation</a>, <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a> or <a class="localLink" href="/FloorPlan">FloorPlan</a>.');

// https://schema.org/numberOfBeds
t("numberOfBeds");
t("The quantity of the given bed type available in the HotelRoom, Suite, House, or Apartment.");

// https://schema.org/numberOfCredits
t("numberOfCredits");
t("The number of credits or units awarded by a Course or required to complete an EducationalOccupationalProgram.");

// https://schema.org/numberOfDoors
t("numberOfDoors");
t("The number of doors.<br/><br/>

Typical unit code(s): C62.");
t("The number of doors.");

// https://schema.org/numberOfEmployees
t("numberOfEmployees");
t("The number of employees in an organization, e.g. business.");

// https://schema.org/numberOfEpisodes
t("numberOfEpisodes");
t("The number of episodes in this season or series.");

// https://schema.org/numberOfForwardGears
t("numberOfForwardGears");
t("The total number of forward gears available for the transmission system of the vehicle.<br/><br/>

Typical unit code(s): C62.");
t("The total number of forward gears available for the transmission system of the vehicle.");

// https://schema.org/numberOfFullBathrooms
t("numberOfFullBathrooms");
t('Number of full bathrooms - The total number of full and ¾ bathrooms in an <a class="localLink" href="/Accommodation">Accommodation</a>. This corresponds to the <a href="https://ddwiki.reso.org/display/DDW17/BathroomsFull+Field">BathroomsFull field in RESO</a>.');
t('Number of full bathrooms - The total number of full and ¾ bathrooms in an <a class="localLink" href="/Accommodation">Accommodation</a>.');

// https://schema.org/numberOfItems
t("numberOfItems");
t("The number of items in an ItemList. Note that some descriptions might not fully describe all items in a list (e.g., multi-page pagination); in such cases, the numberOfItems would be for the entire list.");
t("The number of items in an ItemList.");

// https://schema.org/numberOfLoanPayments
t("numberOfLoanPayments");
t("The number of payments contractually required at origination to repay the loan. For monthly paying loans this is the number of months from the contractual first payment date to the maturity date.");
t("The number of payments contractually required at origination to repay the loan.");

// https://schema.org/numberOfPages
t("numberOfPages");
t("The number of pages in the book.");

// https://schema.org/numberOfPartialBathrooms
t("numberOfPartialBathrooms");
t('Number of partial bathrooms - The total number of half and ¼ bathrooms in an <a class="localLink" href="/Accommodation">Accommodation</a>. This corresponds to the <a href="https://ddwiki.reso.org/display/DDW17/BathroomsPartial+Field">BathroomsPartial field in RESO</a>.');
t('Number of partial bathrooms - The total number of half and ¼ bathrooms in an <a class="localLink" href="/Accommodation">Accommodation</a>.');

// https://schema.org/numberOfPlayers
t("numberOfPlayers");
t("Indicate how many people can play this game (minimum, maximum, or range).");

// https://schema.org/numberOfPreviousOwners
t("numberOfPreviousOwners");
t("The number of owners of the vehicle, including the current one.<br/><br/>

Typical unit code(s): C62.");
t("The number of owners of the vehicle, including the current one.");

// https://schema.org/numberOfRooms
t("numberOfRooms");
t("The number of rooms (excluding bathrooms and closets) of the accommodation or lodging business.
Typical unit code(s): ROM for room or C62 for no unit. The type of room can be put in the unitText property of the QuantitativeValue.");
t("The number of rooms (excluding bathrooms and closets) of the accommodation or lodging business.");

// https://schema.org/numberOfSeasons
t("numberOfSeasons");
t("The number of seasons in this series.");

// https://schema.org/numChildren
t("numChildren");
t("The number of children staying in the unit.");

// https://schema.org/numConstraints
t("numConstraints");
t('Indicates the number of constraints property values defined for a particular <a class="localLink" href="/ConstraintNode">ConstraintNode</a> such as <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>. This helps applications understand if they have access to a sufficiently complete description of a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> or other construct that is defined using properties on template-style nodes.');
t('Indicates the number of constraints property values defined for a particular <a class="localLink" href="/ConstraintNode">ConstraintNode</a> such as <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>.');

// https://schema.org/numTracks
t("numTracks");
t("The number of tracks in this album or playlist.");

// https://schema.org/nutrition
t("nutrition");
t("Nutrition information about the recipe or menu item.");

// https://schema.org/object
t("object");
t("The object upon which the action is carried out, whose state is kept intact or changed. Also known as the semantic roles patient, affected or undergoer (which change their state) or theme (which doesn't). E.g. John read <em>a book</em>.");
t("The object upon which the action is carried out, whose state is kept intact or changed.");

// https://schema.org/observationAbout
t("observationAbout");
t('The <a class="localLink" href="/observationAbout">observationAbout</a> property identifies an entity, often a <a class="localLink" href="/Place">Place</a>, associated with an <a class="localLink" href="/Observation">Observation</a>.');

// https://schema.org/observationDate
t("observationDate");
t('The observationDate of an <a class="localLink" href="/Observation">Observation</a>.');

// https://schema.org/observationPeriod
t("observationPeriod");
t("The length of time an Observation took place over. The format follows <code>P[0-9]*[Y|M|D|h|m|s]</code>. For example, P1Y is Period 1 Year, P3M is Period 3 Months, P3h is Period 3 hours.");
t("The length of time an Observation took place over.");

// https://schema.org/occupancy
t("occupancy");
t("The allowed total occupancy for the accommodation in persons (including infants etc). For individual accommodations, this is not necessarily the legal maximum but defines the permitted usage as per the contractual agreement (e.g. a double room used by a single person).
Typical unit code(s): C62 for person.");
t("The allowed total occupancy for the accommodation in persons (including infants etc).");

// https://schema.org/occupationalCategory
t("occupationalCategory");
t('A category describing the job, preferably using a term from a taxonomy such as <a href="http://www.onetcenter.org/taxonomy.html">BLS O*NET-SOC</a>, <a href="https://www.ilo.org/public/english/bureau/stat/isco/isco08/">ISCO-08</a> or similar, with the property repeated for each applicable value. Ideally the taxonomy should be identified, and both the textual label and formal code for the category should be provided.<br/><br/>

Note: for historical reasons, any textual label and formal code provided as a literal may be assumed to be from O*NET-SOC.');
t('A category describing the job, preferably using a term from a taxonomy such as <a href="http://www.onetcenter.org/taxonomy.html">BLS O*NET-SOC</a>, <a href="https://www.ilo.org/public/english/bureau/stat/isco/isco08/">ISCO-08</a> or similar, with the property repeated for each applicable value.');

// https://schema.org/occupationalCredentialAwarded
t("occupationalCredentialAwarded");
t("A description of the qualification, award, certificate, diploma or other occupational credential awarded as a consequence of successful completion of this course or program.");

// https://schema.org/occupationLocation
t("occupationLocation");
t("The region/country for which this occupational description is appropriate. Note that educational requirements and qualifications can vary between jurisdictions.");
t("The region/country for which this occupational description is appropriate.");

// https://schema.org/offerCount
t("offerCount");
t("The number of offers for the product.");

// https://schema.org/offeredBy
t("offeredBy");
t("A pointer to the organization or person making the offer.");

// https://schema.org/offers
t("offers");
t('An offer to provide this item&#x2014;for example, an offer to sell a product, rent the DVD of a movie, perform a service, or give away tickets to an event. Use <a class="localLink" href="/businessFunction">businessFunction</a> to indicate the kind of transaction offered, i.e. sell, lease, etc. This property can also be used to describe a <a class="localLink" href="/Demand">Demand</a>. While this property is listed as expected on a number of common types, it can be used in others. In that case, using a second type, such as Product or a subtype of Product, can clarify the nature of the offer.');
t("An offer to provide this item&#x2014;for example, an offer to sell a product, rent the DVD of a movie, perform a service, or give away tickets to an event.");

// https://schema.org/offersPrescriptionByMail
t("offersPrescriptionByMail");
t("Whether prescriptions can be delivered by mail.");

// https://schema.org/openingHours
t("openingHours");
t('The general opening hours for a business. Opening hours can be specified as a weekly time range, starting with days, then times per day. Multiple days can be listed with commas \',\' separating each day. Day or time ranges are specified using a hyphen \'-\'.<br/><br/>

<ul>
<li>Days are specified using the following two-letter combinations: <code>Mo</code>, <code>Tu</code>, <code>We</code>, <code>Th</code>, <code>Fr</code>, <code>Sa</code>, <code>Su</code>.</li>
<li>Times are specified using 24:00 format. For example, 3pm is specified as <code>15:00</code>, 10am as <code>10:00</code>. </li>
<li>Here is an example: <code>&lt;time itemprop="openingHours" datetime=&quot;Tu,Th 16:00-20:00&quot;&gt;Tuesdays and Thursdays 4-8pm&lt;/time&gt;</code>.</li>
<li>If a business is open 7 days a week, then it can be specified as <code>&lt;time itemprop=&quot;openingHours&quot; datetime=&quot;Mo-Su&quot;&gt;Monday through Sunday, all day&lt;/time&gt;</code>.</li>
</ul>');
t("The general opening hours for a business.");

// https://schema.org/openingHoursSpecification
t("openingHoursSpecification");
t("The opening hours of a certain place.");

// https://schema.org/opens
t("opens");
t("The opening hour of the place or service on the given day(s) of the week.");

// https://schema.org/operatingSystem
t("operatingSystem");
t("Operating systems supported (Windows 7, OS X 10.6, Android 1.6).");

// https://schema.org/opponent
t("opponent");
t("A sub property of participant. The opponent on this action.");
t("A sub property of participant.");

// https://schema.org/option
t("option");
t("A sub property of object. The options subject to this action.");
t("A sub property of object.");

// https://schema.org/orderDate
t("orderDate");
t("Date order was placed.");

// https://schema.org/orderDelivery
t("orderDelivery");
t("The delivery of the parcel related to this order or order item.");

// https://schema.org/orderedItem
t("orderedItem");
t("The item ordered.");

// https://schema.org/orderItemNumber
t("orderItemNumber");
t("The identifier of the order item.");

// https://schema.org/orderItemStatus
t("orderItemStatus");
t("The current status of the order item.");

// https://schema.org/orderNumber
t("orderNumber");
t("The identifier of the transaction.");

// https://schema.org/orderQuantity
t("orderQuantity");
t("The number of the item ordered. If the property is not set, assume the quantity is one.");
t("The number of the item ordered.");

// https://schema.org/orderStatus
t("orderStatus");
t("The current status of the order.");

// https://schema.org/organizer
t("organizer");
t("An organizer of an Event.");

// https://schema.org/originAddress
t("originAddress");
t("Shipper's address.");

// https://schema.org/originalMediaContextDescription
t("originalMediaContextDescription");
t('Describes, in a <a class="localLink" href="/MediaReview">MediaReview</a> when dealing with <a class="localLink" href="/DecontextualizedContent">DecontextualizedContent</a>, background information that can contribute to better interpretation of the <a class="localLink" href="/MediaObject">MediaObject</a>.');

// https://schema.org/originalMediaLink
t("originalMediaLink");
t('Link to the page containing an original version of the content, or directly to an online copy of the original <a class="localLink" href="/MediaObject">MediaObject</a> content, e.g. video file.');

// https://schema.org/originatesFrom
t("originatesFrom");
t("The vasculature the lymphatic structure originates, or afferents, from.");

// https://schema.org/overdosage
t("overdosage");
t("Any information related to overdose on a drug, including signs or symptoms, treatments, contact information for emergency response.");

// https://schema.org/ownedFrom
t("ownedFrom");
t("The date and time of obtaining the product.");

// https://schema.org/ownedThrough
t("ownedThrough");
t("The date and time of giving up ownership on the product.");

// https://schema.org/ownershipFundingInfo
t("ownershipFundingInfo");
t('For an <a class="localLink" href="/Organization">Organization</a> (often but not necessarily a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>), a description of organizational ownership structure; funding and grants. In a news/media setting, this is with particular reference to editorial independence.   Note that the <a class="localLink" href="/funder">funder</a> is also available and can be used to make basic funder information machine-readable.');
t('For an <a class="localLink" href="/Organization">Organization</a> (often but not necessarily a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>), a description of organizational ownership structure; funding and grants.');

// https://schema.org/owns
t("owns");
t("Products owned by the organization or person.");

// https://schema.org/pageEnd
t("pageEnd");
t('The page on which the work ends; for example "138" or "xvi".');

// https://schema.org/pageStart
t("pageStart");
t('The page on which the work starts; for example "135" or "xiii".');

// https://schema.org/pagination
t("pagination");
t('Any description of pages that is not separated into pageStart and pageEnd; for example, "1-6, 9, 55" or "10-12, 46-49".');

// https://schema.org/parent
t("parent");
t("A parent of this person.");

// https://schema.org/parentItem
t("parentItem");
t('The parent of a question, answer or item in general. Typically used for Q/A discussion threads e.g. a chain of comments with the first comment being an <a class="localLink" href="/Article">Article</a> or other <a class="localLink" href="/CreativeWork">CreativeWork</a>. See also <a class="localLink" href="/comment">comment</a> which points from something to a comment about it.');
t("The parent of a question, answer or item in general.");

// https://schema.org/parentOrganization
t("parentOrganization");
t('The larger organization that this organization is a <a class="localLink" href="/subOrganization">subOrganization</a> of, if any.');

// https://schema.org/parents
t("parents");
t("A parents of the person.");

// https://schema.org/parentService
t("parentService");
t("A broadcast service to which the broadcast service may belong to such as regional variations of a national channel.");

// https://schema.org/parentTaxon
t("parentTaxon");
t("Closest parent taxon of the taxon in question.");

// https://schema.org/participant
t("participant");
t("Other co-agents that participated in the action indirectly. E.g. John wrote a book with <em>Steve</em>.");
t("Other co-agents that participated in the action indirectly.");

// https://schema.org/partOfEpisode
t("partOfEpisode");
t("The episode to which this clip belongs.");

// https://schema.org/partOfInvoice
t("partOfInvoice");
t("The order is being paid as part of the referenced Invoice.");

// https://schema.org/partOfOrder
t("partOfOrder");
t("The overall order the items in this delivery were included in.");

// https://schema.org/partOfSeason
t("partOfSeason");
t("The season to which this episode belongs.");

// https://schema.org/partOfSeries
t("partOfSeries");
t("The series to which this episode or season belongs.");

// https://schema.org/partOfSystem
t("partOfSystem");
t("The anatomical or organ system that this structure is part of.");

// https://schema.org/partOfTrip
t("partOfTrip");
t('Identifies that this <a class="localLink" href="/Trip">Trip</a> is a subTrip of another Trip.  For example Day 1, Day 2, etc. of a multi-day trip.');
t('Identifies that this <a class="localLink" href="/Trip">Trip</a> is a subTrip of another Trip.');

// https://schema.org/partOfTVSeries
t("partOfTVSeries");
t("The TV series to which this episode or season belongs.");

// https://schema.org/partySize
t("partySize");
t("Number of people the reservation should accommodate.");

// https://schema.org/passengerPriorityStatus
t("passengerPriorityStatus");
t("The priority status assigned to a passenger for security or boarding (e.g. FastTrack or Priority).");

// https://schema.org/passengerSequenceNumber
t("passengerSequenceNumber");
t("The passenger's sequence number as assigned by the airline.");

// https://schema.org/pathophysiology
t("pathophysiology");
t("Changes in the normal mechanical, physical, and biochemical functions that are associated with this activity or condition.");

// https://schema.org/pattern
t("pattern");
t("A pattern that something has, for example 'polka dot', 'striped', 'Canadian flag'. Values are typically expressed as text, although links to controlled value schemes are also supported.");
t("A pattern that something has, for example 'polka dot', 'striped', 'Canadian flag'.");

// https://schema.org/payload
t("payload");
t('The permitted weight of passengers and cargo, EXCLUDING the weight of the empty vehicle.<br/><br/>

Typical unit code(s): KGM for kilogram, LBR for pound<br/><br/>

<ul>
<li>Note 1: Many databases specify the permitted TOTAL weight instead, which is the sum of <a class="localLink" href="/weight">weight</a> and <a class="localLink" href="/payload">payload</a></li>
<li>Note 2: You can indicate additional information in the <a class="localLink" href="/name">name</a> of the <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> node.</li>
<li>Note 3: You may also link to a <a class="localLink" href="/QualitativeValue">QualitativeValue</a> node that provides additional information using <a class="localLink" href="/valueReference">valueReference</a>.</li>
<li>Note 4: Note that you can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.</li>
</ul>');
t("The permitted weight of passengers and cargo, EXCLUDING the weight of the empty vehicle.");

// https://schema.org/paymentAccepted
t("paymentAccepted");
t("Cash, Credit Card, Cryptocurrency, Local Exchange Tradings System, etc.");

// https://schema.org/paymentDue
t("paymentDue");
t("The date that payment is due.");

// https://schema.org/paymentDueDate
t("paymentDueDate");
t("The date that payment is due.");

// https://schema.org/paymentMethod
t("paymentMethod");
t("The name of the credit card or other method of payment for the order.");

// https://schema.org/paymentMethodId
t("paymentMethodId");
t("An identifier for the method of payment used (e.g. the last 4 digits of the credit card).");

// https://schema.org/paymentStatus
t("paymentStatus");
t("The status of payment; whether the invoice has been paid or not.");

// https://schema.org/paymentUrl
t("paymentUrl");
t("The URL for sending a payment.");

// https://schema.org/penciler
t("penciler");
t("The individual who draws the primary narrative artwork.");

// https://schema.org/percentile10
t("percentile10");
t("The 10th percentile value.");

// https://schema.org/percentile25
t("percentile25");
t("The 25th percentile value.");

// https://schema.org/percentile75
t("percentile75");
t("The 75th percentile value.");

// https://schema.org/percentile90
t("percentile90");
t("The 90th percentile value.");

// https://schema.org/performer
t("performer");
t("A performer at the event&#x2014;for example, a presenter, musician, musical group or actor.");

// https://schema.org/performerIn
t("performerIn");
t("Event that this person is a performer or participant in.");

// https://schema.org/performers
t("performers");
t("The main performer or performers of the event&#x2014;for example, a presenter, musician, or actor.");

// https://schema.org/performTime
t("performTime");
t('The length of time it takes to perform instructions or a direction (not including time to prepare the supplies), in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 duration format</a>.');

// https://schema.org/permissions
t("permissions");
t("Permission(s) required to run the app (for example, a mobile app may require full internet access or may run only on wifi).");

// https://schema.org/permissionType
t("permissionType");
t("The type of permission granted the person, organization, or audience.");

// https://schema.org/permitAudience
t("permitAudience");
t("The target audience for this permit.");

// https://schema.org/permittedUsage
t("permittedUsage");
t("Indications regarding the permitted usage of the accommodation.");

// https://schema.org/petsAllowed
t("petsAllowed");
t("Indicates whether pets are allowed to enter the accommodation or lodging business. More detailed information can be put in a text value.");
t("Indicates whether pets are allowed to enter the accommodation or lodging business.");

// https://schema.org/phoneticText
t("phoneticText");
t('Representation of a text <a class="localLink" href="/textValue">textValue</a> using the specified <a class="localLink" href="/speechToTextMarkup">speechToTextMarkup</a>. For example the city name of Houston in IPA: /ˈhjuːstən/.');
t('Representation of a text <a class="localLink" href="/textValue">textValue</a> using the specified <a class="localLink" href="/speechToTextMarkup">speechToTextMarkup</a>.');

// https://schema.org/photo
t("photo");
t("A photograph of this place.");

// https://schema.org/photos
t("photos");
t("Photographs of this place.");

// https://schema.org/physicalRequirement
t("physicalRequirement");
t("A description of the types of physical activity associated with the job. Defined terms such as those in O*net may be used, but note that there is no way to specify the level of ability as well as its nature when using a defined term.");
t("A description of the types of physical activity associated with the job.");

// https://schema.org/physiologicalBenefits
t("physiologicalBenefits");
t("Specific physiologic benefits associated to the plan.");

// https://schema.org/pickupLocation
t("pickupLocation");
t("Where a taxi will pick up a passenger or a rental car can be picked up.");

// https://schema.org/pickupTime
t("pickupTime");
t("When a taxi will pick up a passenger or a rental car can be picked up.");

// https://schema.org/playersOnline
t("playersOnline");
t("Number of players on the server.");

// https://schema.org/playerType
t("playerType");
t("Player type required&#x2014;for example, Flash or Silverlight.");

// https://schema.org/playMode
t("playMode");
t("Indicates whether this game is multi-player, co-op or single-player.  The game can be marked as multi-player, co-op and single-player at the same time.");
t("Indicates whether this game is multi-player, co-op or single-player.");

// https://schema.org/polygon
t("polygon");
t("A polygon is the area enclosed by a point-to-point path for which the starting and ending points are the same. A polygon is expressed as a series of four or more space delimited points where the first and final points are identical.");
t("A polygon is the area enclosed by a point-to-point path for which the starting and ending points are the same.");

// https://schema.org/populationType
t("populationType");
t('Indicates the populationType common to all members of a <a class="localLink" href="/StatisticalPopulation">StatisticalPopulation</a> or all cases within the scope of a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>.');

// https://schema.org/position
t("position");
t("The position of an item in a series or sequence of items.");

// https://schema.org/positiveNotes
t("positiveNotes");
t('Provides positive considerations regarding something, for example product highlights or (alongside <a class="localLink" href="/negativeNotes">negativeNotes</a>) pro/con lists for reviews.<br/><br/>

In the case of a <a class="localLink" href="/Review">Review</a>, the property describes the <a class="localLink" href="/itemReviewed">itemReviewed</a> from the perspective of the review; in the case of a <a class="localLink" href="/Product">Product</a>, the product itself is being described.<br/><br/>

The property values can be expressed either as unstructured text (repeated as necessary), or if ordered, as a list (in which case the most positive is at the beginning of the list).');
t('Provides positive considerations regarding something, for example product highlights or (alongside <a class="localLink" href="/negativeNotes">negativeNotes</a>) pro/con lists for reviews.');

// https://schema.org/possibleComplication
t("possibleComplication");
t("A possible unexpected and unfavorable evolution of a medical condition. Complications may include worsening of the signs or symptoms of the disease, extension of the condition to other organ systems, etc.");
t("A possible unexpected and unfavorable evolution of a medical condition.");

// https://schema.org/possibleTreatment
t("possibleTreatment");
t("A possible treatment to address this condition, sign or symptom.");

// https://schema.org/postalCode
t("postalCode");
t("The postal code. For example, 94043.");
t("The postal code.");

// https://schema.org/postalCodeBegin
t("postalCodeBegin");
t("First postal code in a range (included).");

// https://schema.org/postalCodeEnd
t("postalCodeEnd");
t('Last postal code in the range (included). Needs to be after <a class="localLink" href="/postalCodeBegin">postalCodeBegin</a>.');
t("Last postal code in the range (included).");

// https://schema.org/postalCodePrefix
t("postalCodePrefix");
t("A defined range of postal codes indicated by a common textual prefix. Used for non-numeric systems such as UK.");
t("A defined range of postal codes indicated by a common textual prefix.");

// https://schema.org/postalCodeRange
t("postalCodeRange");
t("A defined range of postal codes.");

// https://schema.org/postOfficeBoxNumber
t("postOfficeBoxNumber");
t("The post office box number for PO box addresses.");

// https://schema.org/postOp
t("postOp");
t("A description of the postoperative procedures, care, and/or followups for this device.");

// https://schema.org/potentialAction
t("potentialAction");
t("Indicates a potential Action, which describes an idealized action in which this thing would play an 'object' role.");

// https://schema.org/potentialUse
t("potentialUse");
t("Intended use of the BioChemEntity by humans.");

// https://schema.org/practicesAt
t("practicesAt");
t('A <a class="localLink" href="/MedicalOrganization">MedicalOrganization</a> where the <a class="localLink" href="/IndividualPhysician">IndividualPhysician</a> practices.');

// https://schema.org/predecessorOf
t("predecessorOf");
t("A pointer from a previous, often discontinued variant of the product to its newer variant.");

// https://schema.org/pregnancyCategory
t("pregnancyCategory");
t("Pregnancy category of this drug.");

// https://schema.org/pregnancyWarning
t("pregnancyWarning");
t("Any precaution, guidance, contraindication, etc. related to this drug's use during pregnancy.");

// https://schema.org/preOp
t("preOp");
t("A description of the workup, testing, and other preparations required before implanting this device.");

// https://schema.org/preparation
t("preparation");
t("Typical preparation that a patient must undergo before having the procedure performed.");

// https://schema.org/prepTime
t("prepTime");
t('The length of time it takes to prepare the items to be used in instructions or a direction, in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 duration format</a>.');

// https://schema.org/prescribingInfo
t("prescribingInfo");
t("Link to prescribing information for the drug.");

// https://schema.org/prescriptionStatus
t("prescriptionStatus");
t("Indicates the status of drug prescription, e.g. local catalogs classifications or whether the drug is available by prescription or over-the-counter, etc.");

// https://schema.org/previousItem
t("previousItem");
t("A link to the ListItem that precedes the current one.");

// https://schema.org/previousStartDate
t("previousStartDate");
t("Used in conjunction with eventStatus for rescheduled or cancelled events. This property contains the previously scheduled start date. For rescheduled events, the startDate property should be used for the newly scheduled start date. In the (rare) case of an event that has been postponed and rescheduled multiple times, this field may be repeated.");
t("Used in conjunction with eventStatus for rescheduled or cancelled events.");

// https://schema.org/price
t("price");
t('The offer price of a product, or of a price component when attached to PriceSpecification and its subtypes.<br/><br/>

Usage guidelines:<br/><br/>

<ul>
<li>Use the <a class="localLink" href="/priceCurrency">priceCurrency</a> property (with standard formats: <a href="http://en.wikipedia.org/wiki/ISO_4217">ISO 4217 currency format</a>, e.g. "USD"; <a href="https://en.wikipedia.org/wiki/List_of_cryptocurrencies">Ticker symbol</a> for cryptocurrencies, e.g. "BTC"; well known names for <a href="https://en.wikipedia.org/wiki/Local_exchange_trading_system">Local Exchange Trading Systems</a> (LETS) and other currency types, e.g. "Ithaca HOUR") instead of including <a href="http://en.wikipedia.org/wiki/Dollar_sign#Currencies_that_use_the_dollar_or_peso_sign">ambiguous symbols</a> such as \'$\' in the value.</li>
<li>Use \'.\' (Unicode \'FULL STOP\' (U+002E)) rather than \',\' to indicate a decimal point. Avoid using these symbols as a readability separator.</li>
<li>Note that both <a href="http://www.w3.org/TR/xhtml-rdfa-primer/#using-the-content-attribute">RDFa</a> and Microdata syntax allow the use of a "content=" attribute for publishing simple machine-readable values alongside more human-friendly formatting.</li>
<li>Use values from 0123456789 (Unicode \'DIGIT ZERO\' (U+0030) to \'DIGIT NINE\' (U+0039)) rather than superficially similar Unicode symbols.</li>
</ul>');
t("The offer price of a product, or of a price component when attached to PriceSpecification and its subtypes.");

// https://schema.org/priceComponent
t("priceComponent");
t('This property links to all <a class="localLink" href="/UnitPriceSpecification">UnitPriceSpecification</a> nodes that apply in parallel for the <a class="localLink" href="/CompoundPriceSpecification">CompoundPriceSpecification</a> node.');

// https://schema.org/priceComponentType
t("priceComponentType");
t("Identifies a price component (for example, a line item on an invoice), part of the total price for an offer.");

// https://schema.org/priceCurrency
t("priceCurrency");
t('The currency of the price, or a price component when attached to <a class="localLink" href="/PriceSpecification">PriceSpecification</a> and its subtypes.<br/><br/>

Use standard formats: <a href="http://en.wikipedia.org/wiki/ISO_4217">ISO 4217 currency format</a>, e.g. "USD"; <a href="https://en.wikipedia.org/wiki/List_of_cryptocurrencies">Ticker symbol</a> for cryptocurrencies, e.g. "BTC"; well known names for <a href="https://en.wikipedia.org/wiki/Local_exchange_trading_system">Local Exchange Trading Systems</a> (LETS) and other currency types, e.g. "Ithaca HOUR".');
t('The currency of the price, or a price component when attached to <a class="localLink" href="/PriceSpecification">PriceSpecification</a> and its subtypes.');

// https://schema.org/priceRange
t("priceRange");
t("The price range of the business, for example <code>$$$</code>.");

// https://schema.org/priceSpecification
t("priceSpecification");
t("One or more detailed price specifications, indicating the unit price and delivery or payment charges.");

// https://schema.org/priceType
t("priceType");
t('Defines the type of a price specified for an offered product, for example a list price, a (temporary) sale price or a manufacturer suggested retail price. If multiple prices are specified for an offer the <a class="localLink" href="/priceType">priceType</a> property can be used to identify the type of each such specified price. The value of priceType can be specified as a value from enumeration PriceTypeEnumeration or as a free form text string for price types that are not already predefined in PriceTypeEnumeration.');
t("Defines the type of a price specified for an offered product, for example a list price, a (temporary) sale price or a manufacturer suggested retail price.");

// https://schema.org/priceValidUntil
t("priceValidUntil");
t("The date after which the price is no longer available.");

// https://schema.org/primaryImageOfPage
t("primaryImageOfPage");
t("Indicates the main image on the page.");

// https://schema.org/primaryPrevention
t("primaryPrevention");
t("A preventative therapy used to prevent an initial occurrence of the medical condition, such as vaccination.");

// https://schema.org/printColumn
t("printColumn");
t("The number of the column in which the NewsArticle appears in the print edition.");

// https://schema.org/printEdition
t("printEdition");
t("The edition of the print product in which the NewsArticle appears.");

// https://schema.org/printPage
t("printPage");
t("If this NewsArticle appears in print, this field indicates the name of the page on which the article is found. Please note that this field is intended for the exact page name (e.g. A5, B18).");
t("If this NewsArticle appears in print, this field indicates the name of the page on which the article is found.");

// https://schema.org/printSection
t("printSection");
t("If this NewsArticle appears in print, this field indicates the print section in which the article appeared.");

// https://schema.org/procedure
t("procedure");
t("A description of the procedure involved in setting up, using, and/or installing the device.");

// https://schema.org/procedureType
t("procedureType");
t("The type of procedure, for example Surgical, Noninvasive, or Percutaneous.");

// https://schema.org/processingTime
t("processingTime");
t("Estimated processing time for the service using this channel.");

// https://schema.org/processorRequirements
t("processorRequirements");
t("Processor architecture required to run the application (e.g. IA64).");

// https://schema.org/producer
t("producer");
t("The person or organization who produced the work (e.g. music album, movie, TV/radio series etc.).");

// https://schema.org/produces
t("produces");
t("The tangible thing generated by the service, e.g. a passport, permit, etc.");

// https://schema.org/productGroupID
t("productGroupID");
t("Indicates a textual identifier for a ProductGroup.");

// https://schema.org/productID
t("productID");
t('The product identifier, such as ISBN. For example: <code>meta itemprop="productID" content="isbn:123-456-789"</code>.');
t("The product identifier, such as ISBN.");

// https://schema.org/productionCompany
t("productionCompany");
t("The production company or studio responsible for the item, e.g. series, video game, episode etc.");

// https://schema.org/productionDate
t("productionDate");
t("The date of production of the item, e.g. vehicle.");

// https://schema.org/productReturnDays
t("productReturnDays");
t("The productReturnDays property indicates the number of days (from purchase) within which relevant product return policy is applicable.");

// https://schema.org/productReturnLink
t("productReturnLink");
t("Indicates a Web page or service by URL, for product return.");

// https://schema.org/productSupported
t("productSupported");
t('The product or service this support contact point is related to (such as product support for a particular product line). This can be a specific product or product line (e.g. "iPhone") or a general category of products or services (e.g. "smartphones").');
t("The product or service this support contact point is related to (such as product support for a particular product line).");

// https://schema.org/proficiencyLevel
t("proficiencyLevel");
t("Proficiency needed for this content; expected values: 'Beginner', 'Expert'.");

// https://schema.org/programMembershipUsed
t("programMembershipUsed");
t("Any membership in a frequent flyer, hotel loyalty program, etc. being applied to the reservation.");

// https://schema.org/programmingLanguage
t("programmingLanguage");
t("The computer programming language.");

// https://schema.org/programmingModel
t("programmingModel");
t("Indicates whether API is managed or unmanaged.");

// https://schema.org/programName
t("programName");
t("The program providing the membership.");

// https://schema.org/programPrerequisites
t("programPrerequisites");
t("Prerequisites for enrolling in the program.");

// https://schema.org/programType
t("programType");
t("The type of educational or occupational program. For example, classroom, internship, alternance, etc.");
t("The type of educational or occupational program.");

// https://schema.org/propertyID
t("propertyID");
t("A commonly used identifier for the characteristic represented by the property, e.g. a manufacturer or a standard code for a property. propertyID can be
(1) a prefixed string, mainly meant to be used with standards for product properties; (2) a site-specific, non-prefixed string (e.g. the primary key of the property or the vendor-specific ID of the property), or (3)
a URL indicating the type of the property, either pointing to an external vocabulary, or a Web resource that describes the property (e.g. a glossary entry).
Standards bodies should promote a standard prefix for the identifiers of properties from their standards.");
t("A commonly used identifier for the characteristic represented by the property, e.g. a manufacturer or a standard code for a property.");

// https://schema.org/proprietaryName
t("proprietaryName");
t("Proprietary name given to the diet plan, typically by its originator or creator.");

// https://schema.org/proteinContent
t("proteinContent");
t("The number of grams of protein.");

// https://schema.org/provider
t("provider");
t("The service provider, service operator, or service performer; the goods producer. Another party (a seller) may offer those services or goods on behalf of the provider. A provider may also serve as the seller.");
t("The service provider, service operator, or service performer; the goods producer.");

// https://schema.org/providerMobility
t("providerMobility");
t("Indicates the mobility of a provided service (e.g. 'static', 'dynamic').");

// https://schema.org/providesBroadcastService
t("providesBroadcastService");
t("The BroadcastService offered on this channel.");

// https://schema.org/providesService
t("providesService");
t("The service provided by this channel.");

// https://schema.org/publicAccess
t("publicAccess");
t('A flag to signal that the <a class="localLink" href="/Place">Place</a> is open to public visitors.  If this property is omitted there is no assumed default boolean value.');
t('A flag to signal that the <a class="localLink" href="/Place">Place</a> is open to public visitors.');

// https://schema.org/publication
t("publication");
t("A publication event associated with the item.");

// https://schema.org/publicationType
t("publicationType");
t('The type of the medical article, taken from the US NLM MeSH publication type catalog. See also <a href="http://www.nlm.nih.gov/mesh/pubtypes.html">MeSH documentation</a>.');
t("The type of the medical article, taken from the US NLM MeSH publication type catalog.");

// https://schema.org/publicTransportClosuresInfo
t("publicTransportClosuresInfo");
t("Information about public transport closures.");

// https://schema.org/publishedBy
t("publishedBy");
t("An agent associated with the publication event.");

// https://schema.org/publishedOn
t("publishedOn");
t("A broadcast service associated with the publication event.");

// https://schema.org/publisher
t("publisher");
t("The publisher of the creative work.");

// https://schema.org/publisherImprint
t("publisherImprint");
t("The publishing division which published the comic.");

// https://schema.org/publishingPrinciples
t("publishingPrinciples");
t('The publishingPrinciples property indicates (typically via <a class="localLink" href="/URL">URL</a>) a document describing the editorial principles of an <a class="localLink" href="/Organization">Organization</a> (or individual, e.g. a <a class="localLink" href="/Person">Person</a> writing a blog) that relate to their activities as a publisher, e.g. ethics or diversity policies. When applied to a <a class="localLink" href="/CreativeWork">CreativeWork</a> (e.g. <a class="localLink" href="/NewsArticle">NewsArticle</a>) the principles are those of the party primarily responsible for the creation of the <a class="localLink" href="/CreativeWork">CreativeWork</a>.<br/><br/>

While such policies are most typically expressed in natural language, sometimes related information (e.g. indicating a <a class="localLink" href="/funder">funder</a>) can be expressed using schema.org terminology.');
t('The publishingPrinciples property indicates (typically via <a class="localLink" href="/URL">URL</a>) a document describing the editorial principles of an <a class="localLink" href="/Organization">Organization</a> (or individual, e.g. a <a class="localLink" href="/Person">Person</a> writing a blog) that relate to their activities as a publisher, e.g. ethics or diversity policies.');

// https://schema.org/purchaseDate
t("purchaseDate");
t("The date the item, e.g. vehicle, was purchased by the current owner.");

// https://schema.org/qualifications
t("qualifications");
t("Specific qualifications required for this role or Occupation.");

// https://schema.org/quarantineGuidelines
t("quarantineGuidelines");
t("Guidelines about quarantine rules, e.g. in the context of a pandemic.");

// https://schema.org/query
t("query");
t("A sub property of instrument. The query used on this action.");
t("A sub property of instrument.");

// https://schema.org/quest
t("quest");
t("The task that a player-controlled character, or group of characters may complete in order to gain a reward.");

// https://schema.org/question
t("question");
t("A sub property of object. A question.");
t("A sub property of object.");

// https://schema.org/rangeIncludes
t("rangeIncludes");
t("Relates a property to a class that constitutes (one of) the expected type(s) for values of the property.");

// https://schema.org/ratingCount
t("ratingCount");
t("The count of total number of ratings.");

// https://schema.org/ratingExplanation
t("ratingExplanation");
t('A short explanation (e.g. one to two sentences) providing background context and other information that led to the conclusion expressed in the rating. This is particularly applicable to ratings associated with "fact check" markup using <a class="localLink" href="/ClaimReview">ClaimReview</a>.');
t("A short explanation (e.g. one to two sentences) providing background context and other information that led to the conclusion expressed in the rating.");

// https://schema.org/ratingValue
t("ratingValue");
t("The rating for the content.<br/><br/>

Usage guidelines:<br/><br/>

<ul>
<li>Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.</li>
<li>Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.</li>
</ul>");
t("The rating for the content.");

// https://schema.org/readBy
t("readBy");
t("A person who reads (performs) the audiobook.");

// https://schema.org/readonlyValue
t("readonlyValue");
t('Whether or not a property is mutable.  Default is false. Specifying this for a property that also has a value makes it act similar to a "hidden" input in an HTML form.');
t("Whether or not a property is mutable.");

// https://schema.org/realEstateAgent
t("realEstateAgent");
t("A sub property of participant. The real estate agent involved in the action.");
t("A sub property of participant.");

// https://schema.org/recipe
t("recipe");
t("A sub property of instrument. The recipe/instructions used to perform the action.");
t("A sub property of instrument.");

// https://schema.org/recipeCategory
t("recipeCategory");
t("The category of the recipe—for example, appetizer, entree, etc.");

// https://schema.org/recipeCuisine
t("recipeCuisine");
t("The cuisine of the recipe (for example, French or Ethiopian).");

// https://schema.org/recipeIngredient
t("recipeIngredient");
t("A single ingredient used in the recipe, e.g. sugar, flour or garlic.");

// https://schema.org/recipeInstructions
t("recipeInstructions");
t("A step in making the recipe, in the form of a single item (document, video, etc.) or an ordered list with HowToStep and/or HowToSection items.");

// https://schema.org/recipeYield
t("recipeYield");
t("The quantity produced by the recipe (for example, number of people served, number of servings, etc).");

// https://schema.org/recipient
t("recipient");
t("A sub property of participant. The participant who is at the receiving end of the action.");
t("A sub property of participant.");

// https://schema.org/recognizedBy
t("recognizedBy");
t("An organization that acknowledges the validity, value or utility of a credential. Note: recognition may include a process of quality assurance or accreditation.");
t("An organization that acknowledges the validity, value or utility of a credential.");

// https://schema.org/recognizingAuthority
t("recognizingAuthority");
t("If applicable, the organization that officially recognizes this entity as part of its endorsed system of medicine.");

// https://schema.org/recommendationStrength
t("recommendationStrength");
t("Strength of the guideline's recommendation (e.g. 'class I').");

// https://schema.org/recommendedIntake
t("recommendedIntake");
t("Recommended intake of this supplement for a given population as defined by a specific recommending authority.");

// https://schema.org/recordedAs
t("recordedAs");
t("An audio recording of the work.");

// https://schema.org/recordedAt
t("recordedAt");
t("The Event where the CreativeWork was recorded. The CreativeWork may capture all or part of the event.");
t("The Event where the CreativeWork was recorded.");

// https://schema.org/recordedIn
t("recordedIn");
t("The CreativeWork that captured all or part of this Event.");

// https://schema.org/recordingOf
t("recordingOf");
t("The composition this track is a recording of.");

// https://schema.org/recordLabel
t("recordLabel");
t("The label that issued the release.");

// https://schema.org/recourseLoan
t("recourseLoan");
t("The only way you get the money back in the event of default is the security. Recourse is where you still have the opportunity to go back to the borrower for the rest of the money.");
t("The only way you get the money back in the event of default is the security.");

// https://schema.org/referenceQuantity
t("referenceQuantity");
t("The reference quantity for which a certain price applies, e.g. 1 EUR per 4 kWh of electricity. This property is a replacement for unitOfMeasurement for the advanced cases where the price does not relate to a standard unit.");
t("The reference quantity for which a certain price applies, e.g. 1 EUR per 4 kWh of electricity.");

// https://schema.org/referencesOrder
t("referencesOrder");
t("The Order(s) related to this Invoice. One or more Orders may be combined into a single Invoice.");
t("The Order(s) related to this Invoice.");

// https://schema.org/refundType
t("refundType");
t("A refund type, from an enumerated list.");

// https://schema.org/regionDrained
t("regionDrained");
t("The anatomical or organ system drained by this vessel; generally refers to a specific part of an organ.");

// https://schema.org/regionsAllowed
t("regionsAllowed");
t('The regions where the media is allowed. If not specified, then it\'s assumed to be allowed everywhere. Specify the countries in <a href="http://en.wikipedia.org/wiki/ISO_3166">ISO 3166 format</a>.');
t("The regions where the media is allowed.");

// https://schema.org/relatedAnatomy
t("relatedAnatomy");
t("Anatomical systems or structures that relate to the superficial anatomy.");

// https://schema.org/relatedCondition
t("relatedCondition");
t("A medical condition associated with this anatomy.");

// https://schema.org/relatedDrug
t("relatedDrug");
t("Any other drug related to this one, for example commonly-prescribed alternatives.");

// https://schema.org/relatedLink
t("relatedLink");
t("A link related to this web page, for example to other related web pages.");

// https://schema.org/relatedStructure
t("relatedStructure");
t("Related anatomical structure(s) that are not part of the system but relate or connect to it, such as vascular bundles associated with an organ system.");

// https://schema.org/relatedTherapy
t("relatedTherapy");
t("A medical therapy related to this anatomy.");

// https://schema.org/relatedTo
t("relatedTo");
t("The most generic familial relation.");

// https://schema.org/releaseDate
t("releaseDate");
t("The release date of a product or product model. This can be used to distinguish the exact variant of a product.");
t("The release date of a product or product model.");

// https://schema.org/releasedEvent
t("releasedEvent");
t("The place and time the release was issued, expressed as a PublicationEvent.");

// https://schema.org/releaseNotes
t("releaseNotes");
t("Description of what changed in this version.");

// https://schema.org/releaseOf
t("releaseOf");
t("The album this is a release of.");

// https://schema.org/relevantOccupation
t("relevantOccupation");
t("The Occupation for the JobPosting.");

// https://schema.org/relevantSpecialty
t("relevantSpecialty");
t("If applicable, a medical specialty in which this entity is relevant.");

// https://schema.org/remainingAttendeeCapacity
t("remainingAttendeeCapacity");
t("The number of attendee places for an event that remain unallocated.");

// https://schema.org/renegotiableLoan
t("renegotiableLoan");
t("Whether the terms for payment of interest can be renegotiated during the life of the loan.");

// https://schema.org/repeatCount
t("repeatCount");
t('Defines the number of times a recurring <a class="localLink" href="/Event">Event</a> will take place.');

// https://schema.org/repeatFrequency
t("repeatFrequency");
t('Defines the frequency at which <a class="localLink" href="/Event">Event</a>s will occur according to a schedule <a class="localLink" href="/Schedule">Schedule</a>. The intervals between
      events should be defined as a <a class="localLink" href="/Duration">Duration</a> of time.');
t('Defines the frequency at which <a class="localLink" href="/Event">Event</a>s will occur according to a schedule <a class="localLink" href="/Schedule">Schedule</a>.');

// https://schema.org/repetitions
t("repetitions");
t("Number of times one should repeat the activity.");

// https://schema.org/replacee
t("replacee");
t("A sub property of object. The object that is being replaced.");
t("A sub property of object.");

// https://schema.org/replacer
t("replacer");
t("A sub property of object. The object that replaces.");
t("A sub property of object.");

// https://schema.org/replyToUrl
t("replyToUrl");
t("The URL at which a reply may be posted to the specified UserComment.");

// https://schema.org/reportNumber
t("reportNumber");
t("The number or other unique designator assigned to a Report by the publishing organization.");

// https://schema.org/representativeOfPage
t("representativeOfPage");
t("Indicates whether this image is representative of the content of the page.");

// https://schema.org/requiredCollateral
t("requiredCollateral");
t("Assets required to secure loan or credit repayments. It may take form of third party pledge, goods, financial instruments (cash, securities, etc.)");
t("Assets required to secure loan or credit repayments.");

// https://schema.org/requiredGender
t("requiredGender");
t("Audiences defined by a person's gender.");

// https://schema.org/requiredMaxAge
t("requiredMaxAge");
t("Audiences defined by a person's maximum age.");

// https://schema.org/requiredMinAge
t("requiredMinAge");
t("Audiences defined by a person's minimum age.");

// https://schema.org/requiredQuantity
t("requiredQuantity");
t("The required quantity of the item(s).");

// https://schema.org/requirements
t("requirements");
t("Component dependency requirements for application. This includes runtime environments and shared libraries that are not included in the application distribution package, but required to run the application (examples: DirectX, Java or .NET runtime).");
t("Component dependency requirements for application.");

// https://schema.org/requiresSubscription
t("requiresSubscription");
t("Indicates if use of the media require a subscription  (either paid or free). Allowed values are <code>true</code> or <code>false</code> (note that an earlier version had 'yes', 'no').");
t("Indicates if use of the media require a subscription  (either paid or free).");

// https://schema.org/reservationFor
t("reservationFor");
t("The thing -- flight, event, restaurant, etc. being reserved.");

// https://schema.org/reservationId
t("reservationId");
t("A unique identifier for the reservation.");

// https://schema.org/reservationStatus
t("reservationStatus");
t("The current status of the reservation.");

// https://schema.org/reservedTicket
t("reservedTicket");
t("A ticket associated with the reservation.");

// https://schema.org/responsibilities
t("responsibilities");
t("Responsibilities associated with this role or Occupation.");

// https://schema.org/restockingFee
t("restockingFee");
t('Use <a class="localLink" href="/MonetaryAmount">MonetaryAmount</a> to specify a fixed restocking fee for product returns, or use <a class="localLink" href="/Number">Number</a> to specify a percentage of the product price paid by the customer.');

// https://schema.org/restPeriods
t("restPeriods");
t("How often one should break from the activity.");

// https://schema.org/result
t("result");
t("The result produced in the action. E.g. John wrote <em>a book</em>.");
t("The result produced in the action.");

// https://schema.org/resultComment
t("resultComment");
t("A sub property of result. The Comment created or sent as a result of this action.");
t("A sub property of result.");

// https://schema.org/resultReview
t("resultReview");
t("A sub property of result. The review that resulted in the performing of the action.");
t("A sub property of result.");

// https://schema.org/returnFees
t("returnFees");
t("The type of return fees for purchased products (for any return reason).");

// https://schema.org/returnLabelSource
t("returnLabelSource");
t("The method (from an enumeration) by which the customer obtains a return shipping label for a product returned for any reason.");

// https://schema.org/returnMethod
t("returnMethod");
t("The type of return method offered, specified from an enumeration.");

// https://schema.org/returnPolicyCategory
t("returnPolicyCategory");
t("Specifies an applicable return policy (from an enumeration).");

// https://schema.org/returnPolicyCountry
t("returnPolicyCountry");
t('The country where the product has to be sent to for returns, for example "Ireland" using the <a class="localLink" href="/name">name</a> property of <a class="localLink" href="/Country">Country</a>. You can also provide the two-letter <a href="http://en.wikipedia.org/wiki/ISO_3166-1">ISO 3166-1 alpha-2 country code</a>. Note that this can be different from the country where the product was originally shipped from or sent to.');
t('The country where the product has to be sent to for returns, for example "Ireland" using the <a class="localLink" href="/name">name</a> property of <a class="localLink" href="/Country">Country</a>.');

// https://schema.org/returnPolicySeasonalOverride
t("returnPolicySeasonalOverride");
t("Seasonal override of a return policy.");

// https://schema.org/returnShippingFeesAmount
t("returnShippingFeesAmount");
t('Amount of shipping costs for product returns (for any reason). Applicable when property <a class="localLink" href="/returnFees">returnFees</a> equals <a class="localLink" href="/ReturnShippingFees">ReturnShippingFees</a>.');
t("Amount of shipping costs for product returns (for any reason).");

// https://schema.org/review
t("review");
t("A review of the item.");

// https://schema.org/reviewAspect
t("reviewAspect");
t("This Review or Rating is relevant to this part or facet of the itemReviewed.");

// https://schema.org/reviewBody
t("reviewBody");
t("The actual body of the review.");

// https://schema.org/reviewCount
t("reviewCount");
t("The count of total number of reviews.");

// https://schema.org/reviewedBy
t("reviewedBy");
t("People or organizations that have reviewed the content on this web page for accuracy and/or completeness.");

// https://schema.org/reviewRating
t("reviewRating");
t('The rating given in this review. Note that reviews can themselves be rated. The <code>reviewRating</code> applies to rating given by the review. The <a class="localLink" href="/aggregateRating">aggregateRating</a> property applies to the review itself, as a creative work.');
t("The rating given in this review.");

// https://schema.org/reviews
t("reviews");
t("Review of the item.");

// https://schema.org/riskFactor
t("riskFactor");
t("A modifiable or non-modifiable factor that increases the risk of a patient contracting this condition, e.g. age,  coexisting condition.");

// https://schema.org/risks
t("risks");
t("Specific physiologic risks associated to the diet plan.");

// https://schema.org/roleName
t("roleName");
t("A role played, performed or filled by a person or organization. For example, the team of creators for a comic book might fill the roles named 'inker', 'penciller', and 'letterer'; or an athlete in a SportsTeam might play in the position named 'Quarterback'.");
t("A role played, performed or filled by a person or organization.");

// https://schema.org/roofLoad
t("roofLoad");
t('The permitted total weight of cargo and installations (e.g. a roof rack) on top of the vehicle.<br/><br/>

Typical unit code(s): KGM for kilogram, LBR for pound<br/><br/>

<ul>
<li>Note 1: You can indicate additional information in the <a class="localLink" href="/name">name</a> of the <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> node.</li>
<li>Note 2: You may also link to a <a class="localLink" href="/QualitativeValue">QualitativeValue</a> node that provides additional information using <a class="localLink" href="/valueReference">valueReference</a></li>
<li>Note 3: Note that you can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.</li>
</ul>');
t("The permitted total weight of cargo and installations (e.g. a roof rack) on top of the vehicle.");

// https://schema.org/rsvpResponse
t("rsvpResponse");
t("The response (yes, no, maybe) to the RSVP.");

// https://schema.org/runsTo
t("runsTo");
t("The vasculature the lymphatic structure runs, or efferents, to.");

// https://schema.org/runtime
t("runtime");
t("Runtime platform or script interpreter dependencies (example: Java v1, Python 2.3, .NET Framework 3.0).");
t("Runtime platform or script interpreter dependencies (example: Java v1, Python 2.3, .");

// https://schema.org/runtimePlatform
t("runtimePlatform");
t("Runtime platform or script interpreter dependencies (example: Java v1, Python 2.3, .NET Framework 3.0).");
t("Runtime platform or script interpreter dependencies (example: Java v1, Python 2.3, .");

// https://schema.org/rxcui
t("rxcui");
t("The RxCUI drug identifier from RXNORM.");

// https://schema.org/safetyConsideration
t("safetyConsideration");
t("Any potential safety concern associated with the supplement. May include interactions with other drugs and foods, pregnancy, breastfeeding, known adverse reactions, and documented efficacy of the supplement.");
t("Any potential safety concern associated with the supplement.");

// https://schema.org/salaryCurrency
t("salaryCurrency");
t('The currency (coded using <a href="http://en.wikipedia.org/wiki/ISO_4217">ISO 4217</a>) used for the main salary information in this job posting or for this employee.');

// https://schema.org/salaryUponCompletion
t("salaryUponCompletion");
t("The expected salary upon completing the training.");

// https://schema.org/sameAs
t("sameAs");
t("URL of a reference Web page that unambiguously indicates the item's identity. E.g. the URL of the item's Wikipedia page, Wikidata entry, or official website.");
t("URL of a reference Web page that unambiguously indicates the item's identity.");

// https://schema.org/sampleType
t("sampleType");
t("What type of code sample: full (compile ready) solution, code snippet, inline code, scripts, template.");

// https://schema.org/saturatedFatContent
t("saturatedFatContent");
t("The number of grams of saturated fat.");

// https://schema.org/scheduledPaymentDate
t("scheduledPaymentDate");
t("The date the invoice is scheduled to be paid.");

// https://schema.org/scheduledTime
t("scheduledTime");
t("The time the object is scheduled to.");

// https://schema.org/scheduleTimezone
t("scheduleTimezone");
t('Indicates the timezone for which the time(s) indicated in the <a class="localLink" href="/Schedule">Schedule</a> are given. The value provided should be among those listed in the IANA Time Zone Database.');
t('Indicates the timezone for which the time(s) indicated in the <a class="localLink" href="/Schedule">Schedule</a> are given.');

// https://schema.org/schemaVersion
t("schemaVersion");
t("Indicates (by URL or string) a particular version of a schema used in some CreativeWork. This property was created primarily to
    indicate the use of a specific schema.org release, e.g. <code>10.0</code> as a simple string, or more explicitly via URL, <code>https://schema.org/docs/releases.html#v10.0</code>. There may be situations in which other schemas might usefully be referenced this way, e.g. <code>http://dublincore.org/specifications/dublin-core/dces/1999-07-02/</code> but this has not been carefully explored in the community.");
t("Indicates (by URL or string) a particular version of a schema used in some CreativeWork.");

// https://schema.org/schoolClosuresInfo
t("schoolClosuresInfo");
t("Information about school closures.");

// https://schema.org/screenCount
t("screenCount");
t("The number of screens in the movie theater.");

// https://schema.org/screenshot
t("screenshot");
t("A link to a screenshot image of the app.");

// https://schema.org/sdDatePublished
t("sdDatePublished");
t('Indicates the date on which the current structured data was generated / published. Typically used alongside <a class="localLink" href="/sdPublisher">sdPublisher</a>.');
t("Indicates the date on which the current structured data was generated / published.");

// https://schema.org/sdLicense
t("sdLicense");
t("A license document that applies to this structured data, typically indicated by URL.");

// https://schema.org/sdPublisher
t("sdPublisher");
t('Indicates the party responsible for generating and publishing the current structured data markup, typically in cases where the structured data is derived automatically from existing published content but published on a different site. For example, student projects and open data initiatives often re-publish existing content with more explicitly structured metadata. The
<a class="localLink" href="/sdPublisher">sdPublisher</a> property helps make such practices more explicit.');
t("Indicates the party responsible for generating and publishing the current structured data markup, typically in cases where the structured data is derived automatically from existing published content but published on a different site.");

// https://schema.org/season
t("season");
t("A season in a media series.");

// https://schema.org/seasonNumber
t("seasonNumber");
t("Position of the season within an ordered group of seasons.");

// https://schema.org/seasons
t("seasons");
t("A season in a media series.");

// https://schema.org/seatingCapacity
t("seatingCapacity");
t("The number of persons that can be seated (e.g. in a vehicle), both in terms of the physical space available, and in terms of limitations set by law.<br/><br/>

Typical unit code(s): C62 for persons.");
t("The number of persons that can be seated (e.g. in a vehicle), both in terms of the physical space available, and in terms of limitations set by law.");

// https://schema.org/seatingType
t("seatingType");
t("The type/class of the seat.");

// https://schema.org/seatNumber
t("seatNumber");
t("The location of the reserved seat (e.g., 27).");

// https://schema.org/seatRow
t("seatRow");
t("The row location of the reserved seat (e.g., B).");

// https://schema.org/seatSection
t("seatSection");
t("The section location of the reserved seat (e.g. Orchestra).");

// https://schema.org/secondaryPrevention
t("secondaryPrevention");
t("A preventative therapy used to prevent reoccurrence of the medical condition after an initial episode of the condition.");

// https://schema.org/securityClearanceRequirement
t("securityClearanceRequirement");
t("A description of any security clearance requirements of the job.");

// https://schema.org/securityScreening
t("securityScreening");
t("The type of security screening the passenger is subject to.");

// https://schema.org/seeks
t("seeks");
t("A pointer to products or services sought by the organization or person (demand).");

// https://schema.org/seller
t("seller");
t("An entity which offers (sells / leases / lends / loans) the services / goods.  A seller may also be a provider.");
t("An entity which offers (sells / leases / lends / loans) the services / goods.");

// https://schema.org/sender
t("sender");
t("A sub property of participant. The participant who is at the sending end of the action.");
t("A sub property of participant.");

// https://schema.org/sensoryRequirement
t("sensoryRequirement");
t("A description of any sensory requirements and levels necessary to function on the job, including hearing and vision. Defined terms such as those in O*net may be used, but note that there is no way to specify the level of ability as well as its nature when using a defined term.");
t("A description of any sensory requirements and levels necessary to function on the job, including hearing and vision.");

// https://schema.org/sensoryUnit
t("sensoryUnit");
t("The neurological pathway extension that inputs and sends information to the brain or spinal cord.");

// https://schema.org/serialNumber
t("serialNumber");
t("The serial number or any alphanumeric identifier of a particular product. When attached to an offer, it is a shortcut for the serial number of the product included in the offer.");
t("The serial number or any alphanumeric identifier of a particular product.");

// https://schema.org/seriousAdverseOutcome
t("seriousAdverseOutcome");
t("A possible serious complication and/or serious side effect of this therapy. Serious adverse outcomes include those that are life-threatening; result in death, disability, or permanent damage; require hospitalization or prolong existing hospitalization; cause congenital anomalies or birth defects; or jeopardize the patient and may require medical or surgical intervention to prevent one of the outcomes in this definition.");
t("A possible serious complication and/or serious side effect of this therapy.");

// https://schema.org/serverStatus
t("serverStatus");
t("Status of a game server.");

// https://schema.org/servesCuisine
t("servesCuisine");
t("The cuisine of the restaurant.");

// https://schema.org/serviceArea
t("serviceArea");
t("The geographic area where the service is provided.");

// https://schema.org/serviceAudience
t("serviceAudience");
t("The audience eligible for this service.");

// https://schema.org/serviceLocation
t("serviceLocation");
t("The location (e.g. civic structure, local business, etc.) where a person can go to access the service.");

// https://schema.org/serviceOperator
t("serviceOperator");
t("The operating organization, if different from the provider.  This enables the representation of services that are provided by an organization, but operated by another organization like a subcontractor.");
t("The operating organization, if different from the provider.");

// https://schema.org/serviceOutput
t("serviceOutput");
t("The tangible thing generated by the service, e.g. a passport, permit, etc.");

// https://schema.org/servicePhone
t("servicePhone");
t("The phone number to use to access the service.");

// https://schema.org/servicePostalAddress
t("servicePostalAddress");
t("The address for accessing the service by mail.");

// https://schema.org/serviceSmsNumber
t("serviceSmsNumber");
t("The number to access the service by text message.");

// https://schema.org/serviceType
t("serviceType");
t("The type of service being offered, e.g. veterans' benefits, emergency relief, etc.");

// https://schema.org/serviceUrl
t("serviceUrl");
t("The website to access the service.");

// https://schema.org/servingSize
t("servingSize");
t("The serving size, in terms of the number of volume or mass.");

// https://schema.org/sha256
t("sha256");
t('The <a href="https://en.wikipedia.org/wiki/SHA-2">SHA-2</a> SHA256 hash of the content of the item. For example, a zero-length input has value \'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855\'.');
t('The <a href="https://en.wikipedia.org/wiki/SHA-2">SHA-2</a> SHA256 hash of the content of the item.');

// https://schema.org/sharedContent
t("sharedContent");
t("A CreativeWork such as an image, video, or audio clip shared as part of this posting.");

// https://schema.org/shippingDestination
t("shippingDestination");
t("indicates (possibly multiple) shipping destinations. These can be defined in several ways, e.g. postalCode ranges.");
t("indicates (possibly multiple) shipping destinations.");

// https://schema.org/shippingDetails
t("shippingDetails");
t('Indicates information about the shipping policies and options associated with an <a class="localLink" href="/Offer">Offer</a>.');

// https://schema.org/shippingLabel
t("shippingLabel");
t('Label to match an <a class="localLink" href="/OfferShippingDetails">OfferShippingDetails</a> with a <a class="localLink" href="/ShippingRateSettings">ShippingRateSettings</a> (within the context of a <a class="localLink" href="/shippingSettingsLink">shippingSettingsLink</a> cross-reference).');

// https://schema.org/shippingOrigin
t("shippingOrigin");
t("Indicates the origin of a shipment, i.e. where it should be coming from.");

// https://schema.org/shippingRate
t("shippingRate");
t('The shipping rate is the cost of shipping to the specified destination. Typically, the maxValue and currency values (of the <a class="localLink" href="/MonetaryAmount">MonetaryAmount</a>) are most appropriate.');
t("The shipping rate is the cost of shipping to the specified destination.");

// https://schema.org/shippingSettingsLink
t("shippingSettingsLink");
t('Link to a page containing <a class="localLink" href="/ShippingRateSettings">ShippingRateSettings</a> and <a class="localLink" href="/DeliveryTimeSettings">DeliveryTimeSettings</a> details.');

// https://schema.org/sibling
t("sibling");
t("A sibling of the person.");

// https://schema.org/siblings
t("siblings");
t("A sibling of the person.");

// https://schema.org/signDetected
t("signDetected");
t("A sign detected by the test.");

// https://schema.org/significance
t("significance");
t("The significance associated with the superficial anatomy; as an example, how characteristics of the superficial anatomy can suggest underlying medical conditions or courses of treatment.");

// https://schema.org/significantLink
t("significantLink");
t("One of the more significant URLs on the page. Typically, these are the non-navigation links that are clicked on the most.");
t("One of the more significant URLs on the page.");

// https://schema.org/significantLinks
t("significantLinks");
t("The most significant URLs on the page. Typically, these are the non-navigation links that are clicked on the most.");
t("The most significant URLs on the page.");

// https://schema.org/signOrSymptom
t("signOrSymptom");
t("A sign or symptom of this condition. Signs are objective or physically observable manifestations of the medical condition while symptoms are the subjective experience of the medical condition.");
t("A sign or symptom of this condition.");

// https://schema.org/size
t("size");
t('A standardized size of a product or creative work, specified either through a simple textual string (for example \'XL\', \'32Wx34L\'), a  QuantitativeValue with a unitCode, or a comprehensive and structured <a class="localLink" href="/SizeSpecification">SizeSpecification</a>; in other cases, the <a class="localLink" href="/width">width</a>, <a class="localLink" href="/height">height</a>, <a class="localLink" href="/depth">depth</a> and <a class="localLink" href="/weight">weight</a> properties may be more applicable.');

// https://schema.org/sizeGroup
t("sizeGroup");
t('The size group (also known as "size type") for a product\'s size. Size groups are common in the fashion industry to define size segments and suggested audiences for wearable products. Multiple values can be combined, for example "men\'s big and tall", "petite maternity" or "regular".');
t('The size group (also known as "size type") for a product\'s size.');

// https://schema.org/sizeSystem
t("sizeSystem");
t('The size system used to identify a product\'s size. Typically either a standard (for example, "GS1" or "ISO-EN13402"), country code (for example "US" or "JP"), or a measuring system (for example "Metric" or "Imperial").');
t("The size system used to identify a product's size.");

// https://schema.org/skills
t("skills");
t("A statement of knowledge, skill, ability, task or any other assertion expressing a competency that is desired or required to fulfill this role or to work in this occupation.");

// https://schema.org/sku
t("sku");
t("The Stock Keeping Unit (SKU), i.e. a merchant-specific identifier for a product or service, or the product to which the offer refers.");

// https://schema.org/slogan
t("slogan");
t("A slogan or motto associated with the item.");

// https://schema.org/smiles
t("smiles");
t("A specification in form of a line notation for describing the structure of chemical species using short ASCII strings.  Double bond stereochemistry \ indicators may need to be escaped in the string in formats where the backslash is an escape character.");
t("A specification in form of a line notation for describing the structure of chemical species using short ASCII strings.");

// https://schema.org/smokingAllowed
t("smokingAllowed");
t("Indicates whether it is allowed to smoke in the place, e.g. in the restaurant, hotel or hotel room.");

// https://schema.org/sodiumContent
t("sodiumContent");
t("The number of milligrams of sodium.");

// https://schema.org/softwareAddOn
t("softwareAddOn");
t("Additional content for a software application.");

// https://schema.org/softwareHelp
t("softwareHelp");
t("Software application help.");

// https://schema.org/softwareRequirements
t("softwareRequirements");
t("Component dependency requirements for application. This includes runtime environments and shared libraries that are not included in the application distribution package, but required to run the application (examples: DirectX, Java or .NET runtime).");
t("Component dependency requirements for application.");

// https://schema.org/softwareVersion
t("softwareVersion");
t("Version of the software instance.");

// https://schema.org/sourcedFrom
t("sourcedFrom");
t("The neurological pathway that originates the neurons.");

// https://schema.org/sourceOrganization
t("sourceOrganization");
t("The Organization on whose behalf the creator was working.");

// https://schema.org/spatial
t("spatial");
t('The "spatial" property can be used in cases when more specific properties
(e.g. <a class="localLink" href="/locationCreated">locationCreated</a>, <a class="localLink" href="/spatialCoverage">spatialCoverage</a>, <a class="localLink" href="/contentLocation">contentLocation</a>) are not known to be appropriate.');

// https://schema.org/spatialCoverage
t("spatialCoverage");
t("The spatialCoverage of a CreativeWork indicates the place(s) which are the focus of the content. It is a subproperty of
      contentLocation intended primarily for more technical and detailed materials. For example with a Dataset, it indicates
      areas that the dataset describes: a dataset of New York weather would have spatialCoverage which was the place: the state of New York.");
t("The spatialCoverage of a CreativeWork indicates the place(s) which are the focus of the content.");

// https://schema.org/speakable
t("speakable");
t('Indicates sections of a Web page that are particularly \'speakable\' in the sense of being highlighted as being especially appropriate for text-to-speech conversion. Other sections of a page may also be usefully spoken in particular circumstances; the \'speakable\' property serves to indicate the parts most likely to be generally useful for speech.<br/><br/>

The <em>speakable</em> property can be repeated an arbitrary number of times, with three kinds of possible \'content-locator\' values:<br/><br/>

1.) <em>id-value</em> URL references - uses <em>id-value</em> of an element in the page being annotated. The simplest use of <em>speakable</em> has (potentially relative) URL values, referencing identified sections of the document concerned.<br/><br/>

2.) CSS Selectors - addresses content in the annotated page, e.g. via class attribute. Use the <a class="localLink" href="/cssSelector">cssSelector</a> property.<br/><br/>

3.)  XPaths - addresses content via XPaths (assuming an XML view of the content). Use the <a class="localLink" href="/xpath">xpath</a> property.<br/><br/>

For more sophisticated markup of speakable sections beyond simple ID references, either CSS selectors or XPath expressions to pick out document section(s) as speakable. For this
we define a supporting type, <a class="localLink" href="/SpeakableSpecification">SpeakableSpecification</a>  which is defined to be a possible value of the <em>speakable</em> property.');
t("Indicates sections of a Web page that are particularly 'speakable' in the sense of being highlighted as being especially appropriate for text-to-speech conversion.");

// https://schema.org/specialCommitments
t("specialCommitments");
t("Any special commitments associated with this job posting. Valid entries include VeteranCommit, MilitarySpouseCommit, etc.");
t("Any special commitments associated with this job posting.");

// https://schema.org/specialOpeningHoursSpecification
t("specialOpeningHoursSpecification");
t('The special opening hours of a certain place.<br/><br/>

Use this to explicitly override general opening hours brought in scope by <a class="localLink" href="/openingHoursSpecification">openingHoursSpecification</a> or <a class="localLink" href="/openingHours">openingHours</a>.');
t("The special opening hours of a certain place.");

// https://schema.org/specialty
t("specialty");
t("One of the domain specialities to which this web page's content applies.");

// https://schema.org/speechToTextMarkup
t("speechToTextMarkup");
t('Form of markup used. eg. <a href="https://www.w3.org/TR/speech-synthesis11">SSML</a> or <a href="https://www.wikidata.org/wiki/Property:P898">IPA</a>.');
t("Form of markup used.");

// https://schema.org/speed
t("speed");
t('The speed range of the vehicle. If the vehicle is powered by an engine, the upper limit of the speed range (indicated by <a class="localLink" href="/maxValue">maxValue</a>) should be the maximum speed achievable under regular conditions.<br/><br/>

Typical unit code(s): KMH for km/h, HM for mile per hour (0.447 04 m/s), KNT for knot<br/><br/>

*Note 1: Use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate the range. Typically, the minimal value is zero.
* Note 2: There are many different ways of measuring the speed range. You can link to information about how the given value has been determined using the <a class="localLink" href="/valueReference">valueReference</a> property.');
t("The speed range of the vehicle.");

// https://schema.org/spokenByCharacter
t("spokenByCharacter");
t("The (e.g. fictional) character, Person or Organization to whom the quotation is attributed within the containing CreativeWork.");

// https://schema.org/sponsor
t("sponsor");
t("A person or organization that supports a thing through a pledge, promise, or financial contribution. E.g. a sponsor of a Medical Study or a corporate sponsor of an event.");
t("A person or organization that supports a thing through a pledge, promise, or financial contribution.");

// https://schema.org/sport
t("sport");
t("A type of sport (e.g. Baseball).");

// https://schema.org/sportsActivityLocation
t("sportsActivityLocation");
t("A sub property of location. The sports activity location where this action occurred.");
t("A sub property of location.");

// https://schema.org/sportsEvent
t("sportsEvent");
t("A sub property of location. The sports event where this action occurred.");
t("A sub property of location.");

// https://schema.org/sportsTeam
t("sportsTeam");
t("A sub property of participant. The sports team that participated on this action.");
t("A sub property of participant.");

// https://schema.org/spouse
t("spouse");
t("The person's spouse.");

// https://schema.org/stage
t("stage");
t("The stage of the condition, if applicable.");

// https://schema.org/stageAsNumber
t("stageAsNumber");
t("The stage represented as a number, e.g. 3.");

// https://schema.org/starRating
t("starRating");
t("An official rating for a lodging business or food establishment, e.g. from national associations or standards bodies. Use the author property to indicate the rating organization, e.g. as an Organization with name such as (e.g. HOTREC, DEHOGA, WHR, or Hotelstars).");
t("An official rating for a lodging business or food establishment, e.g. from national associations or standards bodies.");

// https://schema.org/startDate
t("startDate");
t('The start date and time of the item (in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 date format</a>).');

// https://schema.org/startOffset
t("startOffset");
t("The start time of the clip expressed as the number of seconds from the beginning of the work.");

// https://schema.org/startTime
t("startTime");
t("The startTime of something. For a reserved event or service (e.g. FoodEstablishmentReservation), the time that it is expected to start. For actions that span a period of time, when the action was performed. E.g. John wrote a book from <em>January</em> to December. For media, including audio and video, it's the time offset of the start of a clip within a larger file.<br/><br/>

Note that Event uses startDate/endDate instead of startTime/endTime, even when describing dates with times. This situation may be clarified in future revisions.");
t("The startTime of something.");

// https://schema.org/statType
t("statType");
t('Indicates the kind of statistic represented by a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>, e.g. mean, count etc. The value of statType is a property, either from within Schema.org (e.g. <a class="localLink" href="/count">count</a>, <a class="localLink" href="/median">median</a>, <a class="localLink" href="/marginOfError">marginOfError</a>, <a class="localLink" href="/maxValue">maxValue</a>, <a class="localLink" href="/minValue">minValue</a>) or from other compatible (e.g. RDF) systems such as DataCommons.org or Wikidata.org.');

// https://schema.org/status
t("status");
t("The status of the study (enumerated).");

// https://schema.org/steeringPosition
t("steeringPosition");
t("The position of the steering wheel or similar device (mostly for cars).");

// https://schema.org/step
t("step");
t("A single step item (as HowToStep, text, document, video, etc.) or a HowToSection.");

// https://schema.org/steps
t("steps");
t("A single step item (as HowToStep, text, document, video, etc.) or a HowToSection (originally misnamed 'steps'; 'step' is preferred).");

// https://schema.org/stepValue
t("stepValue");
t("The stepValue attribute indicates the granularity that is expected (and required) of the value in a PropertyValueSpecification.");

// https://schema.org/storageRequirements
t("storageRequirements");
t("Storage requirements (free space required).");

// https://schema.org/streetAddress
t("streetAddress");
t("The street address. For example, 1600 Amphitheatre Pkwy.");
t("The street address.");

// https://schema.org/strengthUnit
t("strengthUnit");
t("The units of an active ingredient's strength, e.g. mg.");

// https://schema.org/strengthValue
t("strengthValue");
t("The value of an active ingredient's strength, e.g. 325.");

// https://schema.org/structuralClass
t("structuralClass");
t("The name given to how bone physically connects to each other.");

// https://schema.org/study
t("study");
t("A medical study or trial related to this entity.");

// https://schema.org/studyDesign
t("studyDesign");
t("Specifics about the observational study design (enumerated).");

// https://schema.org/studyLocation
t("studyLocation");
t("The location in which the study is taking/took place.");

// https://schema.org/studySubject
t("studySubject");
t("A subject of the study, i.e. one of the medical conditions, therapies, devices, drugs, etc. investigated by the study.");

// https://schema.org/stupidProperty
t("stupidProperty");
t("This is a StupidProperty! - for testing only.");

// https://schema.org/subEvent
t("subEvent");
t("An Event that is part of this event. For example, a conference event includes many presentations, each of which is a subEvent of the conference.");
t("An Event that is part of this event.");

// https://schema.org/subEvents
t("subEvents");
t("Events that are a part of this event. For example, a conference event includes many presentations, each subEvents of the conference.");
t("Events that are a part of this event.");

// https://schema.org/subjectOf
t("subjectOf");
t("A CreativeWork or Event about this Thing.");

// https://schema.org/subOrganization
t("subOrganization");
t("A relationship between two organizations where the first includes the second, e.g., as a subsidiary. See also: the more specific 'department' property.");
t("A relationship between two organizations where the first includes the second, e.g., as a subsidiary.");

// https://schema.org/subReservation
t("subReservation");
t("The individual reservations included in the package. Typically a repeated property.");
t("The individual reservations included in the package.");

// https://schema.org/subStageSuffix
t("subStageSuffix");
t("The substage, e.g. 'a' for Stage IIIa.");

// https://schema.org/subStructure
t("subStructure");
t("Component (sub-)structure(s) that comprise this anatomical structure.");

// https://schema.org/subTest
t("subTest");
t("A component test of the panel.");

// https://schema.org/subtitleLanguage
t("subtitleLanguage");
t('Languages in which subtitles/captions are available, in <a href="http://tools.ietf.org/html/bcp47">IETF BCP 47 standard format</a>.');

// https://schema.org/subTrip
t("subTrip");
t('Identifies a <a class="localLink" href="/Trip">Trip</a> that is a subTrip of this Trip.  For example Day 1, Day 2, etc. of a multi-day trip.');
t('Identifies a <a class="localLink" href="/Trip">Trip</a> that is a subTrip of this Trip.');

// https://schema.org/successorOf
t("successorOf");
t("A pointer from a newer variant of a product  to its previous, often discontinued predecessor.");

// https://schema.org/sugarContent
t("sugarContent");
t("The number of grams of sugar.");

// https://schema.org/suggestedAge
t("suggestedAge");
t("The age or age range for the intended audience or person, for example 3-12 months for infants, 1-5 years for toddlers.");

// https://schema.org/suggestedAnswer
t("suggestedAnswer");
t("An answer (possibly one of several, possibly incorrect) to a Question, e.g. on a Question/Answer site.");

// https://schema.org/suggestedGender
t("suggestedGender");
t('The suggested gender of the intended person or audience, for example "male", "female", or "unisex".');

// https://schema.org/suggestedMaxAge
t("suggestedMaxAge");
t("Maximum recommended age in years for the audience or user.");

// https://schema.org/suggestedMeasurement
t("suggestedMeasurement");
t("A suggested range of body measurements for the intended audience or person, for example inseam between 32 and 34 inches or height between 170 and 190 cm. Typically found on a size chart for wearable products.");
t("A suggested range of body measurements for the intended audience or person, for example inseam between 32 and 34 inches or height between 170 and 190 cm.");

// https://schema.org/suggestedMinAge
t("suggestedMinAge");
t("Minimum recommended age in years for the audience or user.");

// https://schema.org/suitableForDiet
t("suitableForDiet");
t("Indicates a dietary restriction or guideline for which this recipe or menu item is suitable, e.g. diabetic, halal etc.");

// https://schema.org/superEvent
t("superEvent");
t("An event that this event is a part of. For example, a collection of individual music performances might each have a music festival as their superEvent.");
t("An event that this event is a part of.");

// https://schema.org/supersededBy
t("supersededBy");
t("Relates a term (i.e. a property, class or enumeration) to one that supersedes it.");

// https://schema.org/supply
t("supply");
t("A sub-property of instrument. A supply consumed when performing instructions or a direction.");
t("A sub-property of instrument.");

// https://schema.org/supplyTo
t("supplyTo");
t("The area to which the artery supplies blood.");

// https://schema.org/supportingData
t("supportingData");
t("Supporting data for a SoftwareApplication.");

// https://schema.org/surface
t("surface");
t("A material used as a surface in some artwork, e.g. Canvas, Paper, Wood, Board, etc.");

// https://schema.org/syllabusSections
t("syllabusSections");
t("Indicates (typically several) Syllabus entities that lay out what each section of the overall course will cover.");

// https://schema.org/target
t("target");
t("Indicates a target EntryPoint, or url, for an Action.");

// https://schema.org/targetCollection
t("targetCollection");
t("A sub property of object. The collection target of the action.");
t("A sub property of object.");

// https://schema.org/targetDescription
t("targetDescription");
t("The description of a node in an established educational framework.");

// https://schema.org/targetName
t("targetName");
t("The name of a node in an established educational framework.");

// https://schema.org/targetPlatform
t("targetPlatform");
t("Type of app development: phone, Metro style, desktop, XBox, etc.");

// https://schema.org/targetPopulation
t("targetPopulation");
t("Characteristics of the population for which this is intended, or which typically uses it, e.g. 'adults'.");

// https://schema.org/targetProduct
t("targetProduct");
t("Target Operating System / Product to which the code applies.  If applies to several versions, just the product name can be used.");
t("Target Operating System / Product to which the code applies.");

// https://schema.org/targetUrl
t("targetUrl");
t("The URL of a node in an established educational framework.");

// https://schema.org/taxID
t("taxID");
t("The Tax / Fiscal ID of the organization or person, e.g. the TIN in the US or the CIF/NIF in Spain.");

// https://schema.org/taxonomicRange
t("taxonomicRange");
t("The taxonomic grouping of the organism that expresses, encodes, or in some way related to the BioChemEntity.");

// https://schema.org/taxonRank
t("taxonRank");
t("The taxonomic rank of this taxon given preferably as a URI from a controlled vocabulary – typically the ranks from TDWG TaxonRank ontology or equivalent Wikidata URIs.");

// https://schema.org/teaches
t("teaches");
t("The item being described is intended to help a person learn the competency or learning outcome defined by the referenced term.");

// https://schema.org/telephone
t("telephone");
t("The telephone number.");

// https://schema.org/temporal
t("temporal");
t('The "temporal" property can be used in cases where more specific properties
(e.g. <a class="localLink" href="/temporalCoverage">temporalCoverage</a>, <a class="localLink" href="/dateCreated">dateCreated</a>, <a class="localLink" href="/dateModified">dateModified</a>, <a class="localLink" href="/datePublished">datePublished</a>) are not known to be appropriate.');

// https://schema.org/temporalCoverage
t("temporalCoverage");
t('The temporalCoverage of a CreativeWork indicates the period that the content applies to, i.e. that it describes, either as a DateTime or as a textual string indicating a time period in <a href="https://en.wikipedia.org/wiki/ISO_8601#Time_intervals">ISO 8601 time interval format</a>. In
      the case of a Dataset it will typically indicate the relevant time period in a precise notation (e.g. for a 2011 census dataset, the year 2011 would be written "2011/2012"). Other forms of content, e.g. ScholarlyArticle, Book, TVSeries or TVEpisode, may indicate their temporalCoverage in broader terms - textually or via well-known URL.
      Written works such as books may sometimes have precise temporal coverage too, e.g. a work set in 1939 - 1945 can be indicated in ISO 8601 interval format format via "1939/1945".<br/><br/>

Open-ended date ranges can be written with ".." in place of the end date. For example, "2015-11/.." indicates a range beginning in November 2015 and with no specified final date. This is tentative and might be updated in future when ISO 8601 is officially updated.');
t('The temporalCoverage of a CreativeWork indicates the period that the content applies to, i.e. that it describes, either as a DateTime or as a textual string indicating a time period in <a href="https://en.wikipedia.org/wiki/ISO_8601#Time_intervals">ISO 8601 time interval format</a>.');

// https://schema.org/termCode
t("termCode");
t('A code that identifies this <a class="localLink" href="/DefinedTerm">DefinedTerm</a> within a <a class="localLink" href="/DefinedTermSet">DefinedTermSet</a>.');

// https://schema.org/termDuration
t("termDuration");
t("The amount of time in a term as defined by the institution. A term is a length of time where students take one or more classes. Semesters and quarters are common units for term.");
t("The amount of time in a term as defined by the institution.");

// https://schema.org/termsOfService
t("termsOfService");
t("Human-readable terms of service documentation.");

// https://schema.org/termsPerYear
t("termsPerYear");
t("The number of times terms of study are offered per year. Semesters and quarters are common units for term. For example, if the student can only take 2 semesters for the program in one year, then termsPerYear should be 2.");
t("The number of times terms of study are offered per year.");

// https://schema.org/text
t("text");
t("The textual content of this CreativeWork.");

// https://schema.org/textValue
t("textValue");
t("Text value being annotated.");

// https://schema.org/thumbnail
t("thumbnail");
t("Thumbnail image for an image or video.");

// https://schema.org/thumbnailUrl
t("thumbnailUrl");
t("A thumbnail image relevant to the Thing.");

// https://schema.org/tickerSymbol
t("tickerSymbol");
t("The exchange traded instrument associated with a Corporation object. The tickerSymbol is expressed as an exchange and an instrument name separated by a space character. For the exchange component of the tickerSymbol attribute, we recommend using the controlled vocabulary of Market Identifier Codes (MIC) specified in ISO 15022.");
t("The exchange traded instrument associated with a Corporation object.");

// https://schema.org/ticketedSeat
t("ticketedSeat");
t("The seat associated with the ticket.");

// https://schema.org/ticketNumber
t("ticketNumber");
t("The unique identifier for the ticket.");

// https://schema.org/ticketToken
t("ticketToken");
t("Reference to an asset (e.g., Barcode, QR code image or PDF) usable for entrance.");

// https://schema.org/timeOfDay
t("timeOfDay");
t('The time of day the program normally runs. For example, "evenings".');
t("The time of day the program normally runs.");

// https://schema.org/timeRequired
t("timeRequired");
t("Approximate or typical time it usually takes to work with or through the content of this work for the typical or target audience.");

// https://schema.org/timeToComplete
t("timeToComplete");
t("The expected length of time to complete the program if attending full-time.");

// https://schema.org/tissueSample
t("tissueSample");
t("The type of tissue sample required for the test.");

// https://schema.org/title
t("title");
t("The title of the job.");

// https://schema.org/titleEIDR
t("titleEIDR");
t('An <a href="https://eidr.org/">EIDR</a> (Entertainment Identifier Registry) <a class="localLink" href="/identifier">identifier</a> representing at the most general/abstract level, a work of film or television.<br/><br/>

For example, the motion picture known as "Ghostbusters" has a titleEIDR of  "10.5240/7EC7-228A-510A-053E-CBB8-J". This title (or work) may have several variants, which EIDR calls "edits". See <a class="localLink" href="/editEIDR">editEIDR</a>.<br/><br/>

Since schema.org types like <a class="localLink" href="/Movie">Movie</a>, <a class="localLink" href="/TVEpisode">TVEpisode</a>, <a class="localLink" href="/TVSeason">TVSeason</a>, and <a class="localLink" href="/TVSeries">TVSeries</a> can be used for both works and their multiple expressions, it is possible to use <a class="localLink" href="/titleEIDR">titleEIDR</a> alone (for a general description), or alongside <a class="localLink" href="/editEIDR">editEIDR</a> for a more edit-specific description.');
t('An <a href="https://eidr.org/">EIDR</a> (Entertainment Identifier Registry) <a class="localLink" href="/identifier">identifier</a> representing at the most general/abstract level, a work of film or television.');

// https://schema.org/tocContinuation
t("tocContinuation");
t('A <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a> can have a <a class="localLink" href="/tocContinuation">tocContinuation</a> indicated, which is another <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a> that would be the default next item to play or render.');

// https://schema.org/tocEntry
t("tocEntry");
t('Indicates a <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a> in a <a class="localLink" href="/HyperToc">HyperToc</a>.');

// https://schema.org/toLocation
t("toLocation");
t("A sub property of location. The final location of the object or the agent after the action.");
t("A sub property of location.");

// https://schema.org/tongueWeight
t("tongueWeight");
t('The permitted vertical load (TWR) of a trailer attached to the vehicle. Also referred to as Tongue Load Rating (TLR) or Vertical Load Rating (VLR).<br/><br/>

Typical unit code(s): KGM for kilogram, LBR for pound<br/><br/>

<ul>
<li>Note 1: You can indicate additional information in the <a class="localLink" href="/name">name</a> of the <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> node.</li>
<li>Note 2: You may also link to a <a class="localLink" href="/QualitativeValue">QualitativeValue</a> node that provides additional information using <a class="localLink" href="/valueReference">valueReference</a>.</li>
<li>Note 3: Note that you can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.</li>
</ul>');
t("The permitted vertical load (TWR) of a trailer attached to the vehicle.");

// https://schema.org/tool
t("tool");
t("A sub property of instrument. An object used (but not consumed) when performing instructions or a direction.");
t("A sub property of instrument.");

// https://schema.org/toRecipient
t("toRecipient");
t("A sub property of recipient. The recipient who was directly sent the message.");
t("A sub property of recipient.");

// https://schema.org/torque
t("torque");
t('The torque (turning force) of the vehicle\'s engine.<br/><br/>

Typical unit code(s): NU for newton metre (N m), F17 for pound-force per foot, or F48 for pound-force per inch<br/><br/>

<ul>
<li>Note 1: You can link to information about how the given value has been determined (e.g. reference RPM) using the <a class="localLink" href="/valueReference">valueReference</a> property.</li>
<li>Note 2: You can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.</li>
</ul>');
t("The torque (turning force) of the vehicle's engine.");

// https://schema.org/totalHistoricalEnrollment
t("totalHistoricalEnrollment");
t("The total number of students that have enrolled in the history of the course.");

// https://schema.org/totalJobOpenings
t("totalJobOpenings");
t("The number of positions open for this job posting. Use a positive integer. Do not use if the number of positions is unclear or not known.");
t("The number of positions open for this job posting.");

// https://schema.org/totalPaymentDue
t("totalPaymentDue");
t("The total amount due.");

// https://schema.org/totalPrice
t("totalPrice");
t("The total price for the reservation or ticket, including applicable taxes, shipping, etc.<br/><br/>

Usage guidelines:<br/><br/>

<ul>
<li>Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.</li>
<li>Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.</li>
</ul>");
t("The total price for the reservation or ticket, including applicable taxes, shipping, etc.<br/><br/>

Usage guidelines:<br/><br/>

<ul>
<li>Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.");

// https://schema.org/totalTime
t("totalTime");
t('The total time required to perform instructions or a direction (including time to prepare the supplies), in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 duration format</a>.');

// https://schema.org/tourBookingPage
t("tourBookingPage");
t('A page providing information on how to book a tour of some <a class="localLink" href="/Place">Place</a>, such as an <a class="localLink" href="/Accommodation">Accommodation</a> or <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a> in a real estate setting, as well as other kinds of tours as appropriate.');

// https://schema.org/touristType
t("touristType");
t("Attraction suitable for type(s) of tourist. E.g. children, visitors from a particular country, etc.");
t("Attraction suitable for type(s) of tourist.");

// https://schema.org/track
t("track");
t("A music recording (track)&#x2014;usually a single song. If an ItemList is given, the list should contain items of type MusicRecording.");
t("A music recording (track)&#x2014;usually a single song.");

// https://schema.org/trackingNumber
t("trackingNumber");
t("Shipper tracking number.");

// https://schema.org/trackingUrl
t("trackingUrl");
t("Tracking url for the parcel delivery.");

// https://schema.org/tracks
t("tracks");
t("A music recording (track)&#x2014;usually a single song.");

// https://schema.org/trailer
t("trailer");
t("The trailer of a movie or TV/radio series, season, episode, etc.");

// https://schema.org/trailerWeight
t("trailerWeight");
t('The permitted weight of a trailer attached to the vehicle.<br/><br/>

Typical unit code(s): KGM for kilogram, LBR for pound
* Note 1: You can indicate additional information in the <a class="localLink" href="/name">name</a> of the <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> node.
* Note 2: You may also link to a <a class="localLink" href="/QualitativeValue">QualitativeValue</a> node that provides additional information using <a class="localLink" href="/valueReference">valueReference</a>.
* Note 3: Note that you can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.');
t("The permitted weight of a trailer attached to the vehicle.");

// https://schema.org/trainingSalary
t("trainingSalary");
t("The estimated salary earned while in the program.");

// https://schema.org/trainName
t("trainName");
t("The name of the train (e.g. The Orient Express).");

// https://schema.org/trainNumber
t("trainNumber");
t("The unique identifier for the train.");

// https://schema.org/transcript
t("transcript");
t("If this MediaObject is an AudioObject or VideoObject, the transcript of that object.");

// https://schema.org/transFatContent
t("transFatContent");
t("The number of grams of trans fat.");

// https://schema.org/transitTime
t("transitTime");
t("The typical delay the order has been sent for delivery and the goods reach the final customer. Typical properties: minValue, maxValue, unitCode (d for DAY).");
t("The typical delay the order has been sent for delivery and the goods reach the final customer.");

// https://schema.org/transitTimeLabel
t("transitTimeLabel");
t('Label to match an <a class="localLink" href="/OfferShippingDetails">OfferShippingDetails</a> with a <a class="localLink" href="/DeliveryTimeSettings">DeliveryTimeSettings</a> (within the context of a <a class="localLink" href="/shippingSettingsLink">shippingSettingsLink</a> cross-reference).');

// https://schema.org/translationOfWork
t("translationOfWork");
t("The work that this work has been translated from. E.g. 物种起源 is a translationOf “On the Origin of Species”.");
t("The work that this work has been translated from.");

// https://schema.org/translator
t("translator");
t("Organization or person who adapts a creative work to different languages, regional differences and technical requirements of a target market, or that translates during some event.");

// https://schema.org/transmissionMethod
t("transmissionMethod");
t("How the disease spreads, either as a route or vector, for example 'direct contact', 'Aedes aegypti', etc.");

// https://schema.org/travelBans
t("travelBans");
t("Information about travel bans, e.g. in the context of a pandemic.");

// https://schema.org/trialDesign
t("trialDesign");
t("Specifics about the trial design (enumerated).");

// https://schema.org/tributary
t("tributary");
t("The anatomical or organ system that the vein flows into; a larger structure that the vein connects to.");

// https://schema.org/tripOrigin
t("tripOrigin");
t("The location of origin of the trip, prior to any destination(s).");

// https://schema.org/typeOfBed
t("typeOfBed");
t("The type of bed to which the BedDetail refers, i.e. the type of bed available in the quantity indicated by quantity.");

// https://schema.org/typeOfGood
t("typeOfGood");
t("The product that this structured value is referring to.");

// https://schema.org/typicalAgeRange
t("typicalAgeRange");
t("The typical expected age range, e.g. '7-9', '11-'.");

// https://schema.org/typicalCreditsPerTerm
t("typicalCreditsPerTerm");
t("The number of credits or units a full-time student would be expected to take in 1 term however 'term' is defined by the institution.");

// https://schema.org/typicalTest
t("typicalTest");
t("A medical test typically performed given this condition.");

// https://schema.org/underName
t("underName");
t("The person or organization the reservation or ticket is for.");

// https://schema.org/unitCode
t("unitCode");
t("The unit of measurement given using the UN/CEFACT Common Code (3 characters) or a URL. Other codes than the UN/CEFACT Common Code may be used with a prefix followed by a colon.");
t("The unit of measurement given using the UN/CEFACT Common Code (3 characters) or a URL.");

// https://schema.org/unitText
t("unitText");
t("A string or text indicating the unit of measurement. Useful if you cannot provide a standard unit code for
<a href='unitCode'>unitCode</a>.");
t("A string or text indicating the unit of measurement.");

// https://schema.org/unnamedSourcesPolicy
t("unnamedSourcesPolicy");
t('For an <a class="localLink" href="/Organization">Organization</a> (typically a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a>), a statement about policy on use of unnamed sources and the decision process required.');

// https://schema.org/unsaturatedFatContent
t("unsaturatedFatContent");
t("The number of grams of unsaturated fat.");

// https://schema.org/uploadDate
t("uploadDate");
t("Date (including time if available) when this media object was uploaded to this site.");

// https://schema.org/upvoteCount
t("upvoteCount");
t("The number of upvotes this question, answer or comment has received from the community.");

// https://schema.org/url
t("url");
t("URL of the item.");

// https://schema.org/urlTemplate
t("urlTemplate");
t("An url template (RFC6570) that will be used to construct the target of the execution of the action.");

// https://schema.org/usageInfo
t("usageInfo");
t('The schema.org <a class="localLink" href="/usageInfo">usageInfo</a> property indicates further information about a <a class="localLink" href="/CreativeWork">CreativeWork</a>. This property is applicable both to works that are freely available and to those that require payment or other transactions. It can reference additional information, e.g. community expectations on preferred linking and citation conventions, as well as purchasing details. For something that can be commercially licensed, usageInfo can provide detailed, resource-specific information about licensing options.<br/><br/>

This property can be used alongside the license property which indicates license(s) applicable to some piece of content. The usageInfo property can provide information about other licensing options, e.g. acquiring commercial usage rights for an image that is also available under non-commercial creative commons licenses.');
t('The schema.org <a class="localLink" href="/usageInfo">usageInfo</a> property indicates further information about a <a class="localLink" href="/CreativeWork">CreativeWork</a>.');

// https://schema.org/usedToDiagnose
t("usedToDiagnose");
t("A condition the test is used to diagnose.");

// https://schema.org/userInteractionCount
t("userInteractionCount");
t("The number of interactions for the CreativeWork using the WebSite or SoftwareApplication.");

// https://schema.org/usesDevice
t("usesDevice");
t("Device used to perform the test.");

// https://schema.org/usesHealthPlanIdStandard
t("usesHealthPlanIdStandard");
t('The standard for interpreting the Plan ID. The preferred is "HIOS". See the Centers for Medicare &amp; Medicaid Services for more details.');
t("The standard for interpreting the Plan ID.");

// https://schema.org/usNPI
t("usNPI");
t('A <a href="https://en.wikipedia.org/wiki/National_Provider_Identifier">National Provider Identifier</a> (NPI)
    is a unique 10-digit identification number issued to health care providers in the United States by the Centers for Medicare and Medicaid Services.');

// https://schema.org/utterances
t("utterances");
t('Text of an utterances (spoken words, lyrics etc.) that occurs at a certain section of a media object, represented as a <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a>.');

// https://schema.org/validFor
t("validFor");
t("The duration of validity of a permit or similar thing.");

// https://schema.org/validFrom
t("validFrom");
t("The date when the item becomes valid.");

// https://schema.org/validIn
t("validIn");
t('The geographic area where the item is valid. Applies for example to a <a class="localLink" href="/Permit">Permit</a>, a <a class="localLink" href="/Certification">Certification</a>, or an <a class="localLink" href="/EducationalOccupationalCredential">EducationalOccupationalCredential</a>.');
t("The geographic area where the item is valid.");

// https://schema.org/validThrough
t("validThrough");
t("The date after when the item is not valid. For example the end of an offer, salary period, or a period of opening hours.");
t("The date after when the item is not valid.");

// https://schema.org/validUntil
t("validUntil");
t("The date when the item is no longer valid.");

// https://schema.org/value
t("value");
t('The value of a <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> (including <a class="localLink" href="/Observation">Observation</a>) or property value node.<br/><br/>

<ul>
<li>For <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> and <a class="localLink" href="/MonetaryAmount">MonetaryAmount</a>, the recommended type for values is \'Number\'.</li>
<li>For <a class="localLink" href="/PropertyValue">PropertyValue</a>, it can be \'Text\', \'Number\', \'Boolean\', or \'StructuredValue\'.</li>
<li>Use values from 0123456789 (Unicode \'DIGIT ZERO\' (U+0030) to \'DIGIT NINE\' (U+0039)) rather than superficially similar Unicode symbols.</li>
<li>Use \'.\' (Unicode \'FULL STOP\' (U+002E)) rather than \',\' to indicate a decimal point. Avoid using these symbols as a readability separator.</li>
</ul>');
t('The value of a <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> (including <a class="localLink" href="/Observation">Observation</a>) or property value node.');

// https://schema.org/valueAddedTaxIncluded
t("valueAddedTaxIncluded");
t("Specifies whether the applicable value-added tax (VAT) is included in the price specification or not.");

// https://schema.org/valueMaxLength
t("valueMaxLength");
t("Specifies the allowed range for number of characters in a literal value.");

// https://schema.org/valueMinLength
t("valueMinLength");
t("Specifies the minimum allowed range for number of characters in a literal value.");

// https://schema.org/valueName
t("valueName");
t("Indicates the name of the PropertyValueSpecification to be used in URL templates and form encoding in a manner analogous to HTML's input@name.");

// https://schema.org/valuePattern
t("valuePattern");
t("Specifies a regular expression for testing literal values according to the HTML spec.");

// https://schema.org/valueReference
t("valueReference");
t("A secondary value that provides additional information on the original value, e.g. a reference temperature or a type of measurement.");

// https://schema.org/valueRequired
t("valueRequired");
t("Whether the property must be filled in to complete the action.  Default is false.");
t("Whether the property must be filled in to complete the action.");

// https://schema.org/variableMeasured
t("variableMeasured");
t('The variableMeasured property can indicate (repeated as necessary) the  variables that are measured in some dataset, either described as text or as pairs of identifier and description using PropertyValue, or more explicitly as a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>.');

// https://schema.org/variablesMeasured
t("variablesMeasured");
t('Originally named <a class="localLink" href="/variablesMeasured">variablesMeasured</a>, the <a class="localLink" href="/variableMeasured">variableMeasured</a> property can indicate (repeated as necessary) the  variables that are measured in some dataset, either described as text or as pairs of identifier and description using PropertyValue.');

// https://schema.org/variantCover
t("variantCover");
t('A description of the variant cover
        for the issue, if the issue is a variant printing. For example, "Bryan Hitch
        Variant Cover" or "2nd Printing Variant".');
t("A description of the variant cover
        for the issue, if the issue is a variant printing.");

// https://schema.org/variesBy
t("variesBy");
t('Indicates the property or properties by which the variants in a <a class="localLink" href="/ProductGroup">ProductGroup</a> vary, e.g. their size, color etc. Schema.org properties can be referenced by their short name e.g. "color"; terms defined elsewhere can be referenced with their URIs.');

// https://schema.org/vatID
t("vatID");
t("The Value-added Tax ID of the organization or person.");

// https://schema.org/vehicleConfiguration
t("vehicleConfiguration");
t("A short text indicating the configuration of the vehicle, e.g. '5dr hatchback ST 2.5 MT 225 hp' or 'limited edition'.");

// https://schema.org/vehicleEngine
t("vehicleEngine");
t("Information about the engine or engines of the vehicle.");

// https://schema.org/vehicleIdentificationNumber
t("vehicleIdentificationNumber");
t("The Vehicle Identification Number (VIN) is a unique serial number used by the automotive industry to identify individual motor vehicles.");

// https://schema.org/vehicleInteriorColor
t("vehicleInteriorColor");
t("The color or color combination of the interior of the vehicle.");

// https://schema.org/vehicleInteriorType
t("vehicleInteriorType");
t("The type or material of the interior of the vehicle (e.g. synthetic fabric, leather, wood, etc.). While most interior types are characterized by the material used, an interior type can also be based on vehicle usage or target audience.");
t("The type or material of the interior of the vehicle (e.g. synthetic fabric, leather, wood, etc.).");

// https://schema.org/vehicleModelDate
t("vehicleModelDate");
t("The release date of a vehicle model (often used to differentiate versions of the same make and model).");

// https://schema.org/vehicleSeatingCapacity
t("vehicleSeatingCapacity");
t("The number of passengers that can be seated in the vehicle, both in terms of the physical space available, and in terms of limitations set by law.<br/><br/>

Typical unit code(s): C62 for persons.");
t("The number of passengers that can be seated in the vehicle, both in terms of the physical space available, and in terms of limitations set by law.");

// https://schema.org/vehicleSpecialUsage
t("vehicleSpecialUsage");
t("Indicates whether the vehicle has been used for special purposes, like commercial rental, driving school, or as a taxi. The legislation in many countries requires this information to be revealed when offering a car for sale.");
t("Indicates whether the vehicle has been used for special purposes, like commercial rental, driving school, or as a taxi.");

// https://schema.org/vehicleTransmission
t("vehicleTransmission");
t('The type of component used for transmitting the power from a rotating power source to the wheels or other relevant component(s) ("gearbox" for cars).');

// https://schema.org/vendor
t("vendor");
t("'vendor' is an earlier term for 'seller'.");

// https://schema.org/verificationFactCheckingPolicy
t("verificationFactCheckingPolicy");
t('Disclosure about verification and fact-checking processes for a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a> or other fact-checking <a class="localLink" href="/Organization">Organization</a>.');

// https://schema.org/version
t("version");
t("The version of the CreativeWork embodied by a specified resource.");

// https://schema.org/video
t("video");
t("An embedded video object.");

// https://schema.org/videoFormat
t("videoFormat");
t("The type of screening or video broadcast used (e.g. IMAX, 3D, SD, HD, etc.).");

// https://schema.org/videoFrameSize
t("videoFrameSize");
t("The frame size of the video.");

// https://schema.org/videoQuality
t("videoQuality");
t("The quality of the video.");

// https://schema.org/volumeNumber
t("volumeNumber");
t('Identifies the volume of publication or multi-part work; for example, "iii" or "2".');

// https://schema.org/warning
t("warning");
t("Any FDA or other warnings about the drug (text or URL).");

// https://schema.org/warranty
t("warranty");
t("The warranty promise(s) included in the offer.");

// https://schema.org/warrantyPromise
t("warrantyPromise");
t("The warranty promise(s) included in the offer.");

// https://schema.org/warrantyScope
t("warrantyScope");
t("The scope of the warranty promise.");

// https://schema.org/webCheckinTime
t("webCheckinTime");
t("The time when a passenger can check into the flight online.");

// https://schema.org/webFeed
t("webFeed");
t("The URL for a feed, e.g. associated with a podcast series, blog, or series of date-stamped updates. This is usually RSS or Atom.");
t("The URL for a feed, e.g. associated with a podcast series, blog, or series of date-stamped updates.");

// https://schema.org/weight
t("weight");
t("The weight of the product or person.");

// https://schema.org/weightTotal
t("weightTotal");
t('The permitted total weight of the loaded vehicle, including passengers and cargo and the weight of the empty vehicle.<br/><br/>

Typical unit code(s): KGM for kilogram, LBR for pound<br/><br/>

<ul>
<li>Note 1: You can indicate additional information in the <a class="localLink" href="/name">name</a> of the <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a> node.</li>
<li>Note 2: You may also link to a <a class="localLink" href="/QualitativeValue">QualitativeValue</a> node that provides additional information using <a class="localLink" href="/valueReference">valueReference</a>.</li>
<li>Note 3: Note that you can use <a class="localLink" href="/minValue">minValue</a> and <a class="localLink" href="/maxValue">maxValue</a> to indicate ranges.</li>
</ul>');
t("The permitted total weight of the loaded vehicle, including passengers and cargo and the weight of the empty vehicle.");

// https://schema.org/wheelbase
t("wheelbase");
t("The distance between the centers of the front and rear wheels.<br/><br/>

Typical unit code(s): CMT for centimeters, MTR for meters, INH for inches, FOT for foot/feet.");
t("The distance between the centers of the front and rear wheels.");

// https://schema.org/width
t("width");
t("The width of the item.");

// https://schema.org/winner
t("winner");
t("A sub property of participant. The winner of the action.");
t("A sub property of participant.");

// https://schema.org/wordCount
t("wordCount");
t("The number of words in the text of the Article.");

// https://schema.org/workExample
t("workExample");
t("Example/instance/realization/derivation of the concept of this creative work. E.g. the paperback edition, first edition, or e-book.");
t("Example/instance/realization/derivation of the concept of this creative work.");

// https://schema.org/workFeatured
t("workFeatured");
t("A work featured in some event, e.g. exhibited in an ExhibitionEvent.
       Specific subproperties are available for workPerformed (e.g. a play), or a workPresented (a Movie at a ScreeningEvent).");
t("A work featured in some event, e.g. exhibited in an ExhibitionEvent.");

// https://schema.org/workHours
t("workHours");
t("The typical working hours for this job (e.g. 1st shift, night shift, 8am-5pm).");

// https://schema.org/workload
t("workload");
t("Quantitative measure of the physiologic output of the exercise; also referred to as energy expenditure.");

// https://schema.org/workLocation
t("workLocation");
t("A contact location for a person's place of work.");

// https://schema.org/workPerformed
t("workPerformed");
t("A work performed in some event, for example a play performed in a TheaterEvent.");

// https://schema.org/workPresented
t("workPresented");
t("The movie presented during this event.");

// https://schema.org/worksFor
t("worksFor");
t("Organizations that the person works for.");

// https://schema.org/workTranslation
t("workTranslation");
t("A work that is a translation of the content of this work. E.g. 西遊記 has an English workTranslation “Journey to the West”, a German workTranslation “Monkeys Pilgerfahrt” and a Vietnamese  translation Tây du ký bình khảo.");
t("A work that is a translation of the content of this work.");

// https://schema.org/worstRating
t("worstRating");
t("The lowest value allowed in this rating system. If worstRating is omitted, 1 is assumed.");
t("The lowest value allowed in this rating system.");

// https://schema.org/xpath
t("xpath");
t('An XPath, e.g. of a <a class="localLink" href="/SpeakableSpecification">SpeakableSpecification</a> or <a class="localLink" href="/WebPageElement">WebPageElement</a>. In the latter case, multiple matches within a page can constitute a single conceptual "Web page element".');
t('An XPath, e.g. of a <a class="localLink" href="/SpeakableSpecification">SpeakableSpecification</a> or <a class="localLink" href="/WebPageElement">WebPageElement</a>.');

// https://schema.org/yearBuilt
t("yearBuilt");
t('The year an <a class="localLink" href="/Accommodation">Accommodation</a> was constructed. This corresponds to the <a href="https://ddwiki.reso.org/display/DDW17/YearBuilt+Field">YearBuilt field in RESO</a>.');
t('The year an <a class="localLink" href="/Accommodation">Accommodation</a> was constructed.');

// https://schema.org/yearlyRevenue
t("yearlyRevenue");
t("The size of the business in annual revenue.");

// https://schema.org/yearsInOperation
t("yearsInOperation");
t("The age of the business.");

// https://schema.org/yield
t("yield");
t("The quantity that results by performing instructions. For example, a paper airplane, 10 personalized candles.");
t("The quantity that results by performing instructions.");

// https://schema.org/3DModel
t("3DModel");
t('A 3D model represents some kind of 3D content, which may have <a class="localLink" href="/encoding">encoding</a>s in one or more <a class="localLink" href="/MediaObject">MediaObject</a>s. Many 3D formats are available (e.g. see <a href="https://en.wikipedia.org/wiki/Category:3D_graphics_file_formats">Wikipedia</a>); specific encoding formats can be represented using the <a class="localLink" href="/encodingFormat">encodingFormat</a> property applied to the relevant <a class="localLink" href="/MediaObject">MediaObject</a>. For the
case of a single file published after Zip compression, the convention of appending \'+zip\' to the <a class="localLink" href="/encodingFormat">encodingFormat</a> can be used. Geospatial, AR/VR, artistic/animation, gaming, engineering and scientific content can all be represented using <a class="localLink" href="/3DModel">3DModel</a>.');
t('A 3D model represents some kind of 3D content, which may have <a class="localLink" href="/encoding">encoding</a>s in one or more <a class="localLink" href="/MediaObject">MediaObject</a>s.');

// https://schema.org/Abdomen
t("Abdomen");
t("Abdomen clinical examination.");

// https://schema.org/AboutPage
t("AboutPage");
t("Web page type: About page.");

// https://schema.org/AcceptAction
t("AcceptAction");
t('The act of committing to/adopting an object.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/RejectAction">RejectAction</a>: The antonym of AcceptAction.</li>
</ul>');
t("The act of committing to/adopting an object.");

// https://schema.org/Accommodation
t("Accommodation");
t('An accommodation is a place that can accommodate human beings, e.g. a hotel room, a camping pitch, or a meeting room. Many accommodations are for overnight stays, but this is not a mandatory requirement.
For more specific types of accommodations not defined in schema.org, one can use <a class="localLink" href="/additionalType">additionalType</a> with external vocabularies.
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("An accommodation is a place that can accommodate human beings, e.g. a hotel room, a camping pitch, or a meeting room.");

// https://schema.org/AccountingService
t("AccountingService");
t('Accountancy business.<br/><br/>

As a <a class="localLink" href="/LocalBusiness">LocalBusiness</a> it can be described as a <a class="localLink" href="/provider">provider</a> of one or more <a class="localLink" href="/Service">Service</a>(s).');
t("Accountancy business.");

// https://schema.org/AchieveAction
t("AchieveAction");
t("The act of accomplishing something via previous efforts. It is an instantaneous action rather than an ongoing process.");
t("The act of accomplishing something via previous efforts.");

// https://schema.org/Action
t("Action");
t('An action performed by a direct agent and indirect participants upon a direct object. Optionally happens at a location with the help of an inanimate instrument. The execution of the action may produce a result. Specific action sub-type documentation specifies the exact expectation of each argument/role.<br/><br/>

See also <a href="http://blog.schema.org/2014/04/announcing-schemaorg-actions.html">blog post</a> and <a href="https://schema.org/docs/actions.html">Actions overview document</a>.');
t("An action performed by a direct agent and indirect participants upon a direct object.");

// https://schema.org/ActionAccessSpecification
t("ActionAccessSpecification");
t("A set of requirements that must be fulfilled in order to perform an Action.");

// https://schema.org/ActionStatusType
t("ActionStatusType");
t("The status of an Action.");

// https://schema.org/ActivateAction
t("ActivateAction");
t("The act of starting or activating a device or application (e.g. starting a timer or turning on a flashlight).");

// https://schema.org/ActivationFee
t("ActivationFee");
t("Represents the activation fee part of the total price for an offered product, for example a cellphone contract.");

// https://schema.org/ActiveActionStatus
t("ActiveActionStatus");
t("An in-progress action (e.g., while watching the movie, or driving to a location).");

// https://schema.org/ActiveNotRecruiting
t("ActiveNotRecruiting");
t("Active, but not recruiting new participants.");

// https://schema.org/AddAction
t("AddAction");
t("The act of editing by adding an object to a collection.");

// https://schema.org/AdministrativeArea
t("AdministrativeArea");
t("A geographical region, typically under the jurisdiction of a particular government.");

// https://schema.org/AdultEntertainment
t("AdultEntertainment");
t("An adult entertainment establishment.");

// https://schema.org/AdultOrientedEnumeration
t("AdultOrientedEnumeration");
t("Enumeration of considerations that make a product relevant or potentially restricted for adults only.");

// https://schema.org/AdvertiserContentArticle
t("AdvertiserContentArticle");
t('An <a class="localLink" href="/Article">Article</a> that an external entity has paid to place or to produce to its specifications. Includes <a href="https://en.wikipedia.org/wiki/Advertorial">advertorials</a>, sponsored content, native advertising and other paid content.');
t('An <a class="localLink" href="/Article">Article</a> that an external entity has paid to place or to produce to its specifications.');

// https://schema.org/AerobicActivity
t("AerobicActivity");
t("Physical activity of relatively low intensity that depends primarily on the aerobic energy-generating process; during activity, the aerobic metabolism uses oxygen to adequately meet energy demands during exercise.");

// https://schema.org/AggregateOffer
t("AggregateOffer");
t('When a single product is associated with multiple offers (for example, the same pair of shoes is offered by different merchants), then AggregateOffer can be used.<br/><br/>

Note: AggregateOffers are normally expected to associate multiple offers that all share the same defined <a class="localLink" href="/businessFunction">businessFunction</a> value, or default to http://purl.org/goodrelations/v1#Sell if businessFunction is not explicitly defined.');
t("When a single product is associated with multiple offers (for example, the same pair of shoes is offered by different merchants), then AggregateOffer can be used.");

// https://schema.org/AggregateRating
t("AggregateRating");
t("The average rating based on multiple ratings or reviews.");

// https://schema.org/AgreeAction
t("AgreeAction");
t("The act of expressing a consistency of opinion with the object. An agent agrees to/about an object (a proposition, topic or theme) with participants.");
t("The act of expressing a consistency of opinion with the object.");

// https://schema.org/Airline
t("Airline");
t("An organization that provides flights for passengers.");

// https://schema.org/Airport
t("Airport");
t("An airport.");

// https://schema.org/AlbumRelease
t("AlbumRelease");
t("AlbumRelease.");

// https://schema.org/AlcoholConsideration
t("AlcoholConsideration");
t("Item contains alcohol or promotes alcohol consumption.");

// https://schema.org/AlgorithmicallyEnhancedDigitalSource
t("AlgorithmicallyEnhancedDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/algorithmicallyEnhanced">algorithmically enhanced</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/AlgorithmicMediaDigitalSource
t("AlgorithmicMediaDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/algorithmicMedia">algorithmic media</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/AlignmentObject
t("AlignmentObject");
t('An intangible item that describes an alignment between a learning resource and a node in an educational framework.<br/><br/>

Should not be used where the nature of the alignment can be described using a simple property, for example to express that a resource <a class="localLink" href="/teaches">teaches</a> or <a class="localLink" href="/assesses">assesses</a> a competency.');
t("An intangible item that describes an alignment between a learning resource and a node in an educational framework.");

// https://schema.org/AllergiesHealthAspect
t("AllergiesHealthAspect");
t("Content about the allergy-related aspects of a health topic.");

// https://schema.org/AllocateAction
t("AllocateAction");
t("The act of organizing tasks/objects/events by associating resources to it.");

// https://schema.org/AllWheelDriveConfiguration
t("AllWheelDriveConfiguration");
t("All-wheel Drive is a transmission layout where the engine drives all four wheels.");

// https://schema.org/AmpStory
t("AmpStory");
t("A creative work with a visual storytelling format intended to be viewed online, particularly on mobile devices.");

// https://schema.org/AMRadioChannel
t("AMRadioChannel");
t("A radio channel that uses AM.");

// https://schema.org/AmusementPark
t("AmusementPark");
t("An amusement park.");

// https://schema.org/AnaerobicActivity
t("AnaerobicActivity");
t("Physical activity that is of high-intensity which utilizes the anaerobic metabolism of the body.");

// https://schema.org/AnalysisNewsArticle
t("AnalysisNewsArticle");
t('An AnalysisNewsArticle is a <a class="localLink" href="/NewsArticle">NewsArticle</a> that, while based on factual reporting, incorporates the expertise of the author/producer, offering interpretations and conclusions.');

// https://schema.org/AnatomicalStructure
t("AnatomicalStructure");
t("Any part of the human body, typically a component of an anatomical system. Organs, tissues, and cells are all anatomical structures.");
t("Any part of the human body, typically a component of an anatomical system.");

// https://schema.org/AnatomicalSystem
t("AnatomicalSystem");
t("An anatomical system is a group of anatomical structures that work together to perform a certain task. Anatomical systems, such as organ systems, are one organizing principle of anatomy, and can include circulatory, digestive, endocrine, integumentary, immune, lymphatic, muscular, nervous, reproductive, respiratory, skeletal, urinary, vestibular, and other systems.");
t("An anatomical system is a group of anatomical structures that work together to perform a certain task.");

// https://schema.org/AndroidPlatform
t("AndroidPlatform");
t("Represents the broad notion of Android-based operating systems.");

// https://schema.org/Anesthesia
t("Anesthesia");
t("A specific branch of medical science that pertains to study of anesthetics and their application.");

// https://schema.org/AnimalShelter
t("AnimalShelter");
t("Animal shelter.");

// https://schema.org/Answer
t("Answer");
t("An answer offered to a question; perhaps correct, perhaps opinionated or wrong.");

// https://schema.org/Apartment
t("Apartment");
t('An apartment (in American English) or flat (in British English) is a self-contained housing unit (a type of residential real estate) that occupies only part of a building (source: Wikipedia, the free encyclopedia, see <a href="http://en.wikipedia.org/wiki/Apartment">http://en.wikipedia.org/wiki/Apartment</a>).');
t("An apartment (in American English) or flat (in British English) is a self-contained housing unit (a type of residential real estate) that occupies only part of a building.");

// https://schema.org/ApartmentComplex
t("ApartmentComplex");
t("Residence type: Apartment complex.");

// https://schema.org/APIReference
t("APIReference");
t("Reference documentation for application programming interfaces (APIs).");

// https://schema.org/Appearance
t("Appearance");
t("Appearance assessment with clinical examination.");

// https://schema.org/AppendAction
t("AppendAction");
t("The act of inserting at the end if an ordered collection.");

// https://schema.org/ApplyAction
t("ApplyAction");
t('The act of registering to an organization/service without the guarantee to receive it.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/RegisterAction">RegisterAction</a>: Unlike RegisterAction, ApplyAction has no guarantees that the application will be accepted.</li>
</ul>');
t("The act of registering to an organization/service without the guarantee to receive it.");

// https://schema.org/ApprovedIndication
t("ApprovedIndication");
t("An indication for a medical therapy that has been formally specified or approved by a regulatory body that regulates use of the therapy; for example, the US FDA approves indications for most drugs in the US.");

// https://schema.org/Aquarium
t("Aquarium");
t("Aquarium.");

// https://schema.org/ArchiveComponent
t("ArchiveComponent");
t("An intangible type to be applied to any archive content, carrying with it a set of properties required to describe archival items and collections.");

// https://schema.org/ArchiveOrganization
t("ArchiveOrganization");
t("An organization with archival holdings. An organization which keeps and preserves archival material and typically makes it accessible to the public.");
t("An organization with archival holdings.");

// https://schema.org/ArriveAction
t("ArriveAction");
t("The act of arriving at a place. An agent arrives at a destination from a fromLocation, optionally with participants.");
t("The act of arriving at a place.");

// https://schema.org/Artery
t("Artery");
t("A type of blood vessel that specifically carries blood away from the heart.");

// https://schema.org/ArtGallery
t("ArtGallery");
t("An art gallery.");

// https://schema.org/Article
t("Article");
t('An article, such as a news article or piece of investigative report. Newspapers and magazines have articles of many different types and this is intended to cover them all.<br/><br/>

See also <a href="http://blog.schema.org/2014/09/schemaorg-support-for-bibliographic_2.html">blog post</a>.');
t("An article, such as a news article or piece of investigative report.");

// https://schema.org/AskAction
t("AskAction");
t('The act of posing a question / favor to someone.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/ReplyAction">ReplyAction</a>: Appears generally as a response to AskAction.</li>
</ul>');
t("The act of posing a question / favor to someone.");

// https://schema.org/AskPublicNewsArticle
t("AskPublicNewsArticle");
t('A <a class="localLink" href="/NewsArticle">NewsArticle</a> expressing an open call by a <a class="localLink" href="/NewsMediaOrganization">NewsMediaOrganization</a> asking the public for input, insights, clarifications, anecdotes, documentation, etc., on an issue, for reporting purposes.');

// https://schema.org/AssessAction
t("AssessAction");
t("The act of forming one's opinion, reaction or sentiment.");

// https://schema.org/AssignAction
t("AssignAction");
t("The act of allocating an action/event/task to some destination (someone or something).");

// https://schema.org/Atlas
t("Atlas");
t("A collection or bound volume of maps, charts, plates or tables, physical or in media form illustrating any subject.");

// https://schema.org/Attorney
t("Attorney");
t('Professional service: Attorney. <br/><br/>

This type is deprecated - <a class="localLink" href="/LegalService">LegalService</a> is more inclusive and less ambiguous.');
t("Professional service: Attorney.");

// https://schema.org/Audience
t("Audience");
t("Intended audience for an item, i.e. the group for whom the item was created.");

// https://schema.org/Audiobook
t("Audiobook");
t("An audiobook.");

// https://schema.org/AudiobookFormat
t("AudiobookFormat");
t("Book format: Audiobook. This is an enumerated value for use with the bookFormat property. There is also a type 'Audiobook' in the bib extension which includes Audiobook specific properties.");
t("Book format: Audiobook.");

// https://schema.org/AudioObject
t("AudioObject");
t("An audio file.");

// https://schema.org/AudioObjectSnapshot
t("AudioObjectSnapshot");
t('A specific and exact (byte-for-byte) version of an <a class="localLink" href="/AudioObject">AudioObject</a>. Two byte-for-byte identical files, for the purposes of this type, considered identical. If they have different embedded metadata the files will differ. Different external facts about the files, e.g. creator or dateCreated that aren\'t represented in their actual content, do not affect this notion of identity.');
t('A specific and exact (byte-for-byte) version of an <a class="localLink" href="/AudioObject">AudioObject</a>.');

// https://schema.org/AuthoritativeLegalValue
t("AuthoritativeLegalValue");
t('Indicates that the publisher gives some special status to the publication of the document. ("The Queens Printer" version of a UK Act of Parliament, or the PDF version of a Directive published by the EU Office of Publications.) Something "Authoritative" is considered to be also <a class="localLink" href="/OfficialLegalValue">OfficialLegalValue</a>.');
t("Indicates that the publisher gives some special status to the publication of the document.");

// https://schema.org/AuthorizeAction
t("AuthorizeAction");
t("The act of granting permission to an object.");

// https://schema.org/AutoBodyShop
t("AutoBodyShop");
t("Auto body shop.");

// https://schema.org/AutoDealer
t("AutoDealer");
t("An car dealership.");

// https://schema.org/AutomatedTeller
t("AutomatedTeller");
t("ATM/cash machine.");

// https://schema.org/AutomotiveBusiness
t("AutomotiveBusiness");
t("Car repair, sales, or parts.");

// https://schema.org/AutoPartsStore
t("AutoPartsStore");
t("An auto parts store.");

// https://schema.org/AutoRental
t("AutoRental");
t("A car rental business.");

// https://schema.org/AutoRepair
t("AutoRepair");
t("Car repair business.");

// https://schema.org/AutoWash
t("AutoWash");
t("A car wash business.");

// https://schema.org/Ayurvedic
t("Ayurvedic");
t("A system of medicine that originated in India over thousands of years and that focuses on integrating and balancing the body, mind, and spirit.");

// https://schema.org/BackgroundNewsArticle
t("BackgroundNewsArticle");
t('A <a class="localLink" href="/NewsArticle">NewsArticle</a> providing historical context, definition and detail on a specific topic (aka "explainer" or "backgrounder"). For example, an in-depth article or frequently-asked-questions (<a href="https://en.wikipedia.org/wiki/FAQ">FAQ</a>) document on topics such as Climate Change or the European Union. Other kinds of background material from a non-news setting are often described using <a class="localLink" href="/Book">Book</a> or <a class="localLink" href="/Article">Article</a>, in particular <a class="localLink" href="/ScholarlyArticle">ScholarlyArticle</a>. See also <a class="localLink" href="/NewsArticle">NewsArticle</a> for related vocabulary from a learning/education perspective.');
t('A <a class="localLink" href="/NewsArticle">NewsArticle</a> providing historical context, definition and detail on a specific topic (aka "explainer" or "backgrounder").');

// https://schema.org/BackOrder
t("BackOrder");
t("Indicates that the item is available on back order.");

// https://schema.org/Bacteria
t("Bacteria");
t("Pathogenic bacteria that cause bacterial infection.");

// https://schema.org/Bakery
t("Bakery");
t("A bakery.");

// https://schema.org/Balance
t("Balance");
t("Physical activity that is engaged to help maintain posture and balance.");

// https://schema.org/BankAccount
t("BankAccount");
t("A product or service offered by a bank whereby one may deposit, withdraw or transfer money and in some cases be paid interest.");

// https://schema.org/BankOrCreditUnion
t("BankOrCreditUnion");
t("Bank or credit union.");

// https://schema.org/Barcode
t("Barcode");
t("An image of a visual machine-readable code such as a barcode or QR code.");

// https://schema.org/BarOrPub
t("BarOrPub");
t("A bar or pub.");

// https://schema.org/BasicIncome
t("BasicIncome");
t("BasicIncome: this is a benefit for basic income.");

// https://schema.org/Beach
t("Beach");
t("Beach.");

// https://schema.org/BeautySalon
t("BeautySalon");
t("Beauty salon.");

// https://schema.org/BedAndBreakfast
t("BedAndBreakfast");
t('Bed and breakfast.
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("Bed and breakfast.");

// https://schema.org/BedDetails
t("BedDetails");
t('An entity holding detailed information about the available bed types, e.g. the quantity of twin beds for a hotel room. For the single case of just one bed of a certain type, you can use bed directly with a text. See also <a class="localLink" href="/BedType">BedType</a> (under development).');
t("An entity holding detailed information about the available bed types, e.g. the quantity of twin beds for a hotel room.");

// https://schema.org/BedType
t("BedType");
t("A type of bed. This is used for indicating the bed or beds available in an accommodation.");
t("A type of bed.");

// https://schema.org/BefriendAction
t("BefriendAction");
t('The act of forming a personal connection with someone (object) mutually/bidirectionally/symmetrically.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/FollowAction">FollowAction</a>: Unlike FollowAction, BefriendAction implies that the connection is reciprocal.</li>
</ul>');
t("The act of forming a personal connection with someone (object) mutually/bidirectionally/symmetrically.");

// https://schema.org/BenefitsHealthAspect
t("BenefitsHealthAspect");
t("Content about the benefits and advantages of usage or utilization of topic.");

// https://schema.org/BikeStore
t("BikeStore");
t("A bike store.");

// https://schema.org/BioChemEntity
t("BioChemEntity");
t("Any biological, chemical, or biochemical thing. For example: a protein; a gene; a chemical; a synthetic chemical.");
t("Any biological, chemical, or biochemical thing.");

// https://schema.org/Blog
t("Blog");
t('A <a href="https://en.wikipedia.org/wiki/Blog">blog</a>, sometimes known as a "weblog". Note that the individual posts (<a class="localLink" href="/BlogPosting">BlogPosting</a>s) in a <a class="localLink" href="/Blog">Blog</a> are often colloquially referred to by the same term.');
t('A <a href="https://en.wikipedia.org/wiki/Blog">blog</a>, sometimes known as a "weblog".');

// https://schema.org/BlogPosting
t("BlogPosting");
t("A blog post.");

// https://schema.org/BloodTest
t("BloodTest");
t("A medical test performed on a sample of a patient's blood.");

// https://schema.org/BoardingPolicyType
t("BoardingPolicyType");
t("A type of boarding policy used by an airline.");

// https://schema.org/BoatReservation
t("BoatReservation");
t('A reservation for boat travel.<br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations. For offers of tickets, use <a class="localLink" href="/Offer">Offer</a>.');
t("A reservation for boat travel.");

// https://schema.org/BoatTerminal
t("BoatTerminal");
t("A terminal for boats, ships, and other water vessels.");

// https://schema.org/BoatTrip
t("BoatTrip");
t("A trip on a commercial ferry line.");

// https://schema.org/BodyMeasurementArm
t("BodyMeasurementArm");
t("Arm length (measured between arms/shoulder line intersection and the prominent wrist bone). Used, for example, to fit shirts.");
t("Arm length (measured between arms/shoulder line intersection and the prominent wrist bone).");

// https://schema.org/BodyMeasurementBust
t("BodyMeasurementBust");
t("Maximum girth of bust. Used, for example, to fit women's suits.");
t("Maximum girth of bust.");

// https://schema.org/BodyMeasurementChest
t("BodyMeasurementChest");
t("Maximum girth of chest. Used, for example, to fit men's suits.");
t("Maximum girth of chest.");

// https://schema.org/BodyMeasurementFoot
t("BodyMeasurementFoot");
t("Foot length (measured between end of the most prominent toe and the most prominent part of the heel). Used, for example, to measure socks.");
t("Foot length (measured between end of the most prominent toe and the most prominent part of the heel).");

// https://schema.org/BodyMeasurementHand
t("BodyMeasurementHand");
t("Maximum hand girth (measured over the knuckles of the open right hand excluding thumb, fingers together). Used, for example, to fit gloves.");
t("Maximum hand girth (measured over the knuckles of the open right hand excluding thumb, fingers together).");

// https://schema.org/BodyMeasurementHead
t("BodyMeasurementHead");
t("Maximum girth of head above the ears. Used, for example, to fit hats.");
t("Maximum girth of head above the ears.");

// https://schema.org/BodyMeasurementHeight
t("BodyMeasurementHeight");
t("Body height (measured between crown of head and soles of feet). Used, for example, to fit jackets.");
t("Body height (measured between crown of head and soles of feet).");

// https://schema.org/BodyMeasurementHips
t("BodyMeasurementHips");
t("Girth of hips (measured around the buttocks). Used, for example, to fit skirts.");
t("Girth of hips (measured around the buttocks).");

// https://schema.org/BodyMeasurementInsideLeg
t("BodyMeasurementInsideLeg");
t("Inside leg (measured between crotch and soles of feet). Used, for example, to fit pants.");
t("Inside leg (measured between crotch and soles of feet).");

// https://schema.org/BodyMeasurementNeck
t("BodyMeasurementNeck");
t("Girth of neck. Used, for example, to fit shirts.");
t("Girth of neck.");

// https://schema.org/BodyMeasurementTypeEnumeration
t("BodyMeasurementTypeEnumeration");
t("Enumerates types (or dimensions) of a person's body measurements, for example for fitting of clothes.");

// https://schema.org/BodyMeasurementUnderbust
t("BodyMeasurementUnderbust");
t("Girth of body just below the bust. Used, for example, to fit women's swimwear.");
t("Girth of body just below the bust.");

// https://schema.org/BodyMeasurementWaist
t("BodyMeasurementWaist");
t("Girth of natural waistline (between hip bones and lower ribs). Used, for example, to fit pants.");
t("Girth of natural waistline (between hip bones and lower ribs).");

// https://schema.org/BodyMeasurementWeight
t("BodyMeasurementWeight");
t("Body weight. Used, for example, to measure pantyhose.");
t("Body weight.");

// https://schema.org/BodyOfWater
t("BodyOfWater");
t("A body of water, such as a sea, ocean, or lake.");

// https://schema.org/Bone
t("Bone");
t("Rigid connective tissue that comprises up the skeletal structure of the human body.");

// https://schema.org/Book
t("Book");
t("A book.");

// https://schema.org/BookFormatType
t("BookFormatType");
t("The publication format of the book.");

// https://schema.org/BookmarkAction
t("BookmarkAction");
t("An agent bookmarks/flags/labels/tags/marks an object.");

// https://schema.org/BookSeries
t("BookSeries");
t("A series of books. Included books can be indicated with the hasPart property.");
t("A series of books.");

// https://schema.org/BookStore
t("BookStore");
t("A bookstore.");

// https://schema.org/Boolean
t("Boolean");
t("Boolean: True or False.");

// https://schema.org/BorrowAction
t("BorrowAction");
t('The act of obtaining an object under an agreement to return it at a later date. Reciprocal of LendAction.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/LendAction">LendAction</a>: Reciprocal of BorrowAction.</li>
</ul>');
t("The act of obtaining an object under an agreement to return it at a later date.");

// https://schema.org/BowlingAlley
t("BowlingAlley");
t("A bowling alley.");

// https://schema.org/BrainStructure
t("BrainStructure");
t("Any anatomical structure which pertains to the soft nervous tissue functioning as the coordinating center of sensation and intellectual and nervous activity.");

// https://schema.org/Brand
t("Brand");
t("A brand is a name used by an organization or business person for labeling a product, product group, or similar.");

// https://schema.org/BreadcrumbList
t("BreadcrumbList");
t('A BreadcrumbList is an ItemList consisting of a chain of linked Web pages, typically described using at least their URL and their name, and typically ending with the current page.<br/><br/>

The <a class="localLink" href="/position">position</a> property is used to reconstruct the order of the items in a BreadcrumbList. The convention is that a breadcrumb list has an <a class="localLink" href="/itemListOrder">itemListOrder</a> of <a class="localLink" href="/ItemListOrderAscending">ItemListOrderAscending</a> (lower values listed first), and that the first items in this list correspond to the "top" or beginning of the breadcrumb trail, e.g. with a site or section homepage. The specific values of \'position\' are not assigned meaning for a BreadcrumbList, but they should be integers, e.g. beginning with \'1\' for the first item in the list.');
t("A BreadcrumbList is an ItemList consisting of a chain of linked Web pages, typically described using at least their URL and their name, and typically ending with the current page.");

// https://schema.org/Brewery
t("Brewery");
t("Brewery.");

// https://schema.org/Bridge
t("Bridge");
t("A bridge.");

// https://schema.org/BroadcastChannel
t("BroadcastChannel");
t("A unique instance of a BroadcastService on a CableOrSatelliteService lineup.");

// https://schema.org/BroadcastEvent
t("BroadcastEvent");
t("An over the air or online broadcast event.");

// https://schema.org/BroadcastFrequencySpecification
t("BroadcastFrequencySpecification");
t("The frequency in MHz and the modulation used for a particular BroadcastService.");

// https://schema.org/BroadcastRelease
t("BroadcastRelease");
t("BroadcastRelease.");

// https://schema.org/BroadcastService
t("BroadcastService");
t("A delivery service through which content is provided via broadcast over the air or online.");

// https://schema.org/BrokerageAccount
t("BrokerageAccount");
t("An account that allows an investor to deposit funds and place investment orders with a licensed broker or brokerage firm.");

// https://schema.org/BuddhistTemple
t("BuddhistTemple");
t("A Buddhist temple.");

// https://schema.org/BusinessAudience
t("BusinessAudience");
t("A set of characteristics belonging to businesses, e.g. who compose an item's target audience.");

// https://schema.org/BusinessEntityType
t("BusinessEntityType");
t("A business entity type is a conceptual entity representing the legal form, the size, the main line of business, the position in the value chain, or any combination thereof, of an organization or business person.<br/><br/>

Commonly used values:<br/><br/>

<ul>
<li>http://purl.org/goodrelations/v1#Business</li>
<li>http://purl.org/goodrelations/v1#Enduser</li>
<li>http://purl.org/goodrelations/v1#PublicInstitution</li>
<li>http://purl.org/goodrelations/v1#Reseller</li>
</ul>");
t("A business entity type is a conceptual entity representing the legal form, the size, the main line of business, the position in the value chain, or any combination thereof, of an organization or business person.");

// https://schema.org/BusinessEvent
t("BusinessEvent");
t("Event type: Business event.");

// https://schema.org/BusinessFunction
t("BusinessFunction");
t("The business function specifies the type of activity or access (i.e., the bundle of rights) offered by the organization or business person through the offer. Typical are sell, rental or lease, maintenance or repair, manufacture / produce, recycle / dispose, engineering / construction, or installation. Proprietary specifications of access rights are also instances of this class.<br/><br/>

Commonly used values:<br/><br/>

<ul>
<li>http://purl.org/goodrelations/v1#ConstructionInstallation</li>
<li>http://purl.org/goodrelations/v1#Dispose</li>
<li>http://purl.org/goodrelations/v1#LeaseOut</li>
<li>http://purl.org/goodrelations/v1#Maintain</li>
<li>http://purl.org/goodrelations/v1#ProvideService</li>
<li>http://purl.org/goodrelations/v1#Repair</li>
<li>http://purl.org/goodrelations/v1#Sell</li>
<li>http://purl.org/goodrelations/v1#Buy</li>
</ul>");
t("The business function specifies the type of activity or access (i.e., the bundle of rights) offered by the organization or business person through the offer.");

// https://schema.org/BusinessSupport
t("BusinessSupport");
t("BusinessSupport: this is a benefit for supporting businesses.");

// https://schema.org/BusOrCoach
t("BusOrCoach");
t("A bus (also omnibus or autobus) is a road vehicle designed to carry passengers. Coaches are luxury busses, usually in service for long distance travel.");
t("A bus (also omnibus or autobus) is a road vehicle designed to carry passengers.");

// https://schema.org/BusReservation
t("BusReservation");
t('A reservation for bus travel. <br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations. For offers of tickets, use <a class="localLink" href="/Offer">Offer</a>.');
t("A reservation for bus travel.");

// https://schema.org/BusStation
t("BusStation");
t("A bus station.");

// https://schema.org/BusStop
t("BusStop");
t("A bus stop.");

// https://schema.org/BusTrip
t("BusTrip");
t("A trip on a commercial bus line.");

// https://schema.org/BuyAction
t("BuyAction");
t("The act of giving money to a seller in exchange for goods or services rendered. An agent buys an object, product, or service from a seller for a price. Reciprocal of SellAction.");
t("The act of giving money to a seller in exchange for goods or services rendered.");

// https://schema.org/CableOrSatelliteService
t("CableOrSatelliteService");
t("A service which provides access to media programming like TV or radio. Access may be via cable or satellite.");
t("A service which provides access to media programming like TV or radio.");

// https://schema.org/CafeOrCoffeeShop
t("CafeOrCoffeeShop");
t("A cafe or coffee shop.");

// https://schema.org/Campground
t("Campground");
t('A camping site, campsite, or <a class="localLink" href="/Campground">Campground</a> is a place used for overnight stay in the outdoors, typically containing individual <a class="localLink" href="/CampingPitch">CampingPitch</a> locations. <br/><br/>

In British English a campsite is an area, usually divided into a number of pitches, where people can camp overnight using tents or camper vans or caravans; this British English use of the word is synonymous with the American English expression campground. In American English the term campsite generally means an area where an individual, family, group, or military unit can pitch a tent or park a camper; a campground may contain many campsites (source: Wikipedia, see <a href="https://en.wikipedia.org/wiki/Campsite">https://en.wikipedia.org/wiki/Campsite</a>).<br/><br/>

See also the dedicated <a href="/docs/hotels.html">document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t('A camping site, campsite, or <a class="localLink" href="/Campground">Campground</a> is a place used for overnight stay in the outdoors, typically containing individual <a class="localLink" href="/CampingPitch">CampingPitch</a> locations.');

// https://schema.org/CampingPitch
t("CampingPitch");
t('A <a class="localLink" href="/CampingPitch">CampingPitch</a> is an individual place for overnight stay in the outdoors, typically being part of a larger camping site, or <a class="localLink" href="/Campground">Campground</a>.<br/><br/>

In British English a campsite, or campground, is an area, usually divided into a number of pitches, where people can camp overnight using tents or camper vans or caravans; this British English use of the word is synonymous with the American English expression campground. In American English the term campsite generally means an area where an individual, family, group, or military unit can pitch a tent or park a camper; a campground may contain many campsites.
(Source: Wikipedia, see <a href="https://en.wikipedia.org/wiki/Campsite">https://en.wikipedia.org/wiki/Campsite</a>.)<br/><br/>

See also the dedicated <a href="/docs/hotels.html">document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t('A <a class="localLink" href="/CampingPitch">CampingPitch</a> is an individual place for overnight stay in the outdoors, typically being part of a larger camping site, or <a class="localLink" href="/Campground">Campground</a>.');

// https://schema.org/Canal
t("Canal");
t("A canal, like the Panama Canal.");

// https://schema.org/CancelAction
t("CancelAction");
t('The act of asserting that a future event/action is no longer going to happen.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/ConfirmAction">ConfirmAction</a>: The antonym of CancelAction.</li>
</ul>');
t("The act of asserting that a future event/action is no longer going to happen.");

// https://schema.org/Car
t("Car");
t("A car is a wheeled, self-powered motor vehicle used for transportation.");

// https://schema.org/Cardiovascular
t("Cardiovascular");
t("A specific branch of medical science that pertains to diagnosis and treatment of disorders of heart and vasculature.");

// https://schema.org/CardiovascularExam
t("CardiovascularExam");
t("Cardiovascular system assessment with clinical examination.");

// https://schema.org/CarUsageType
t("CarUsageType");
t("A value indicating a special usage of a car, e.g. commercial rental, driving school, or as a taxi.");

// https://schema.org/CaseSeries
t("CaseSeries");
t("A case series (also known as a clinical series) is a medical research study that tracks patients with a known exposure given similar treatment or examines their medical records for exposure and outcome. A case series can be retrospective or prospective and usually involves a smaller number of patients than the more powerful case-control studies or randomized controlled trials. Case series may be consecutive or non-consecutive, depending on whether all cases presenting to the reporting authors over a period of time were included, or only a selection.");
t("A case series (also known as a clinical series) is a medical research study that tracks patients with a known exposure given similar treatment or examines their medical records for exposure and outcome.");

// https://schema.org/Casino
t("Casino");
t("A casino.");

// https://schema.org/CassetteFormat
t("CassetteFormat");
t("CassetteFormat.");

// https://schema.org/CategoryCode
t("CategoryCode");
t("A Category Code.");

// https://schema.org/CategoryCodeSet
t("CategoryCodeSet");
t("A set of Category Code values.");

// https://schema.org/CatholicChurch
t("CatholicChurch");
t("A Catholic church.");

// https://schema.org/CausesHealthAspect
t("CausesHealthAspect");
t("Information about the causes and main actions that gave rise to the topic.");

// https://schema.org/CDCPMDRecord
t("CDCPMDRecord");
t('A CDCPMDRecord is a data structure representing a record in a CDC tabular data format
      used for hospital data reporting. See <a href="/docs/cdc-covid.html">documentation</a> for details, and the linked CDC materials for authoritative
      definitions used as the source here.');
t("A CDCPMDRecord is a data structure representing a record in a CDC tabular data format
      used for hospital data reporting.");

// https://schema.org/CDFormat
t("CDFormat");
t("CDFormat.");

// https://schema.org/Cemetery
t("Cemetery");
t("A graveyard.");

// https://schema.org/Certification
t("Certification");
t('A Certification is an official and authoritative statement about a subject, for example a product, service, person, or organization. A certification is typically issued by an indendent certification body, for example a professional organization or government. It formally attests certain characteristics about the subject, for example Organizations can be ISO certified, Food products can be certified Organic or Vegan, a Person can be a certified professional, a Place can be certified for food processing. There are certifications for many domains: regulatory, organizational, recycling, food, efficiency, educational, ecological, etc. A certification is a form of credential, as are accreditations and licenses. Mapped from the <a href="https://www.gs1.org/voc/CertificationDetails">gs1:CertificationDetails</a> class in the GS1 Web Vocabulary.');
t("A Certification is an official and authoritative statement about a subject, for example a product, service, person, or organization.");

// https://schema.org/CertificationActive
t("CertificationActive");
t("Specifies that a certification is active.");

// https://schema.org/CertificationInactive
t("CertificationInactive");
t("Specifies that a certification is inactive (no longer in effect).");

// https://schema.org/CertificationStatusEnumeration
t("CertificationStatusEnumeration");
t("Enumerates the different statuses of a Certification (Active and Inactive).");

// https://schema.org/Chapter
t("Chapter");
t("One of the sections into which a book is divided. A chapter usually has a section number or a name.");
t("One of the sections into which a book is divided.");

// https://schema.org/CharitableIncorporatedOrganization
t("CharitableIncorporatedOrganization");
t("CharitableIncorporatedOrganization: Non-profit type referring to a Charitable Incorporated Organization (UK).");

// https://schema.org/CheckAction
t("CheckAction");
t("An agent inspects, determines, investigates, inquires, or examines an object's accuracy, quality, condition, or state.");

// https://schema.org/CheckInAction
t("CheckInAction");
t('The act of an agent communicating (service provider, social media, etc) their arrival by registering/confirming for a previously reserved service (e.g. flight check-in) or at a place (e.g. hotel), possibly resulting in a result (boarding pass, etc).<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/CheckOutAction">CheckOutAction</a>: The antonym of CheckInAction.</li>
<li><a class="localLink" href="/ArriveAction">ArriveAction</a>: Unlike ArriveAction, CheckInAction implies that the agent is informing/confirming the start of a previously reserved service.</li>
<li><a class="localLink" href="/ConfirmAction">ConfirmAction</a>: Unlike ConfirmAction, CheckInAction implies that the agent is informing/confirming the <em>start</em> of a previously reserved service rather than its validity/existence.</li>
</ul>');
t("The act of an agent communicating (service provider, social media, etc) their arrival by registering/confirming for a previously reserved service (e.g. flight check-in) or at a place (e.g. hotel), possibly resulting in a result (boarding pass, etc).");

// https://schema.org/CheckOutAction
t("CheckOutAction");
t('The act of an agent communicating (service provider, social media, etc) their departure of a previously reserved service (e.g. flight check-in) or place (e.g. hotel).<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/CheckInAction">CheckInAction</a>: The antonym of CheckOutAction.</li>
<li><a class="localLink" href="/DepartAction">DepartAction</a>: Unlike DepartAction, CheckOutAction implies that the agent is informing/confirming the end of a previously reserved service.</li>
<li><a class="localLink" href="/CancelAction">CancelAction</a>: Unlike CancelAction, CheckOutAction implies that the agent is informing/confirming the end of a previously reserved service.</li>
</ul>');
t("The act of an agent communicating (service provider, social media, etc) their departure of a previously reserved service (e.g. flight check-in) or place (e.g. hotel).");

// https://schema.org/CheckoutPage
t("CheckoutPage");
t("Web page type: Checkout page.");

// https://schema.org/ChemicalSubstance
t("ChemicalSubstance");
t('A chemical substance is \'a portion of matter of constant composition, composed of molecular entities of the same type or of different types\' (source: <a href="https://www.ebi.ac.uk/chebi/searchId.do?chebiId=59999">ChEBI:59999</a>).');

// https://schema.org/ChildCare
t("ChildCare");
t("A Childcare center.");

// https://schema.org/ChildrensEvent
t("ChildrensEvent");
t("Event type: Children's event.");

// https://schema.org/Chiropractic
t("Chiropractic");
t("A system of medicine focused on the relationship between the body's structure, mainly the spine, and its functioning.");

// https://schema.org/ChooseAction
t("ChooseAction");
t("The act of expressing a preference from a set of options or a large or unbounded set of choices/options.");

// https://schema.org/Church
t("Church");
t("A church.");

// https://schema.org/City
t("City");
t("A city or town.");

// https://schema.org/CityHall
t("CityHall");
t("A city hall.");

// https://schema.org/CivicStructure
t("CivicStructure");
t("A public structure, such as a town hall or concert hall.");

// https://schema.org/Claim
t("Claim");
t('A <a class="localLink" href="/Claim">Claim</a> in Schema.org represents a specific, factually-oriented claim that could be the <a class="localLink" href="/itemReviewed">itemReviewed</a> in a <a class="localLink" href="/ClaimReview">ClaimReview</a>. The content of a claim can be summarized with the <a class="localLink" href="/text">text</a> property. Variations on well known claims can have their common identity indicated via <a class="localLink" href="/sameAs">sameAs</a> links, and summarized with a <a class="localLink" href="/name">name</a>. Ideally, a <a class="localLink" href="/Claim">Claim</a> description includes enough contextual information to minimize the risk of ambiguity or inclarity. In practice, many claims are better understood in the context in which they appear or the interpretations provided by claim reviews.<br/><br/>

Beyond <a class="localLink" href="/ClaimReview">ClaimReview</a>, the Claim type can be associated with related creative works - for example a <a class="localLink" href="/ScholarlyArticle">ScholarlyArticle</a> or <a class="localLink" href="/Question">Question</a> might be <a class="localLink" href="/about">about</a> some <a class="localLink" href="/Claim">Claim</a>.<br/><br/>

At this time, Schema.org does not define any types of relationship between claims. This is a natural area for future exploration.');
t('A <a class="localLink" href="/Claim">Claim</a> in Schema.org represents a specific, factually-oriented claim that could be the <a class="localLink" href="/itemReviewed">itemReviewed</a> in a <a class="localLink" href="/ClaimReview">ClaimReview</a>.');

// https://schema.org/ClaimReview
t("ClaimReview");
t("A fact-checking review of claims made (or reported) in some creative work (referenced via itemReviewed).");

// https://schema.org/Class
t("Class");
t("A class, also often called a 'Type'; equivalent to rdfs:Class.");

// https://schema.org/CleaningFee
t("CleaningFee");
t("Represents the cleaning fee part of the total price for an offered product, for example a vacation rental.");

// https://schema.org/Clinician
t("Clinician");
t("Medical clinicians, including practicing physicians and other medical professionals involved in clinical practice.");

// https://schema.org/Clip
t("Clip");
t("A short TV or radio program or a segment/part of a program.");

// https://schema.org/ClothingStore
t("ClothingStore");
t("A clothing store.");

// https://schema.org/Code
t("Code");
t("Computer programming source code. Example: Full (compile ready) solutions, code snippet samples, scripts, templates.");
t("Computer programming source code.");

// https://schema.org/CohortStudy
t("CohortStudy");
t("Also known as a panel study. A cohort study is a form of longitudinal study used in medicine and social science. It is one type of study design and should be compared with a cross-sectional study.  A cohort is a group of people who share a common characteristic or experience within a defined period (e.g., are born, leave school, lose their job, are exposed to a drug or a vaccine, etc.). The comparison group may be the general population from which the cohort is drawn, or it may be another cohort of persons thought to have had little or no exposure to the substance under investigation, but otherwise similar. Alternatively, subgroups within the cohort may be compared with each other.");
t("Also known as a panel study.");

// https://schema.org/Collection
t("Collection");
t("A collection of items, e.g. creative works or products.");

// https://schema.org/CollectionPage
t("CollectionPage");
t("Web page type: Collection page.");

// https://schema.org/CollegeOrUniversity
t("CollegeOrUniversity");
t("A college, university, or other third-level educational institution.");

// https://schema.org/ComedyClub
t("ComedyClub");
t("A comedy club.");

// https://schema.org/ComedyEvent
t("ComedyEvent");
t("Event type: Comedy event.");

// https://schema.org/ComicCoverArt
t("ComicCoverArt");
t("The artwork on the cover of a comic.");

// https://schema.org/ComicIssue
t("ComicIssue");
t("Individual comic issues are serially published as
        part of a larger series. For the sake of consistency, even one-shot issues
        belong to a series comprised of a single issue. All comic issues can be
        uniquely identified by: the combination of the name and volume number of the
        series to which the issue belongs; the issue number; and the variant
        description of the issue (if any).");
t("Individual comic issues are serially published as
        part of a larger series.");

// https://schema.org/ComicSeries
t("ComicSeries");
t('A sequential publication of comic stories under a
        unifying title, for example "The Amazing Spider-Man" or "Groo the
        Wanderer".');

// https://schema.org/ComicStory
t("ComicStory");
t('The term "story" is any indivisible, re-printable
        unit of a comic, including the interior stories, covers, and backmatter. Most
        comics have at least two stories: a cover (ComicCoverArt) and an interior story.');
t('The term "story" is any indivisible, re-printable
        unit of a comic, including the interior stories, covers, and backmatter.');

// https://schema.org/Comment
t("Comment");
t('A comment on an item - for example, a comment on a blog post. The comment\'s content is expressed via the <a class="localLink" href="/text">text</a> property, and its topic via <a class="localLink" href="/about">about</a>, properties shared with all CreativeWorks.');
t("A comment on an item - for example, a comment on a blog post.");

// https://schema.org/CommentAction
t("CommentAction");
t("The act of generating a comment about a subject.");

// https://schema.org/CommentPermission
t("CommentPermission");
t("Permission to add comments to the document.");

// https://schema.org/CommunicateAction
t("CommunicateAction");
t("The act of conveying information to another person via a communication medium (instrument) such as speech, email, or telephone conversation.");

// https://schema.org/CommunityHealth
t("CommunityHealth");
t("A field of public health focusing on improving health characteristics of a defined population in relation with their geographical or environment areas.");

// https://schema.org/CompilationAlbum
t("CompilationAlbum");
t("CompilationAlbum.");

// https://schema.org/Completed
t("Completed");
t("Completed.");

// https://schema.org/CompletedActionStatus
t("CompletedActionStatus");
t("An action that has already taken place.");

// https://schema.org/CompleteDataFeed
t("CompleteDataFeed");
t('A <a class="localLink" href="/CompleteDataFeed">CompleteDataFeed</a> is a <a class="localLink" href="/DataFeed">DataFeed</a> whose standard representation includes content for every item currently in the feed.<br/><br/>

This is the equivalent of Atom\'s element as defined in Feed Paging and Archiving <a href="https://tools.ietf.org/html/rfc5005">RFC 5005</a>, for example (and as defined for Atom), when using data from a feed that represents a collection of items that varies over time (e.g. "Top Twenty Records") there is no need to have newer entries mixed in alongside older, obsolete entries. By marking this feed as a CompleteDataFeed, old entries can be safely discarded when the feed is refreshed, since we can assume the feed has provided descriptions for all current items.');
t('A <a class="localLink" href="/CompleteDataFeed">CompleteDataFeed</a> is a <a class="localLink" href="/DataFeed">DataFeed</a> whose standard representation includes content for every item currently in the feed.');

// https://schema.org/CompositeCaptureDigitalSource
t("CompositeCaptureDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/compositeCapture">composite capture</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/CompositeSyntheticDigitalSource
t("CompositeSyntheticDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/compositeSynthetic">composite synthetic</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/CompositeWithTrainedAlgorithmicMediaDigitalSource
t("CompositeWithTrainedAlgorithmicMediaDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/compositeWithTrainedAlgorithmicMedia">composite with trained algorithmic media</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/CompoundPriceSpecification
t("CompoundPriceSpecification");
t('A compound price specification is one that bundles multiple prices that all apply in combination for different dimensions of consumption. Use the name property of the attached unit price specification for indicating the dimension of a price component (e.g. "electricity" or "final cleaning").');
t("A compound price specification is one that bundles multiple prices that all apply in combination for different dimensions of consumption.");

// https://schema.org/ComputerLanguage
t("ComputerLanguage");
t('This type covers computer programming languages such as Scheme and Lisp, as well as other language-like computer representations. Natural languages are best represented with the <a class="localLink" href="/Language">Language</a> type.');
t("This type covers computer programming languages such as Scheme and Lisp, as well as other language-like computer representations.");

// https://schema.org/ComputerStore
t("ComputerStore");
t("A computer store.");

// https://schema.org/ConfirmAction
t("ConfirmAction");
t('The act of notifying someone that a future event/action is going to happen as expected.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/CancelAction">CancelAction</a>: The antonym of ConfirmAction.</li>
</ul>');
t("The act of notifying someone that a future event/action is going to happen as expected.");

// https://schema.org/Consortium
t("Consortium");
t('A Consortium is a membership <a class="localLink" href="/Organization">Organization</a> whose members are typically Organizations.');

// https://schema.org/ConstraintNode
t("ConstraintNode");
t('The ConstraintNode type is provided to support usecases in which a node in a structured data graph is described with properties which appear to describe a single entity, but are being used in a situation where they serve a more abstract purpose. A <a class="localLink" href="/ConstraintNode">ConstraintNode</a> can be described using <a class="localLink" href="/constraintProperty">constraintProperty</a> and <a class="localLink" href="/numConstraints">numConstraints</a>. These constraint properties can serve a
    variety of purposes, and their values may sometimes be understood to indicate sets of possible values rather than single, exact and specific values.');
t("The ConstraintNode type is provided to support usecases in which a node in a structured data graph is described with properties which appear to describe a single entity, but are being used in a situation where they serve a more abstract purpose.");

// https://schema.org/ConsumeAction
t("ConsumeAction");
t("The act of ingesting information/resources/food.");

// https://schema.org/ContactPage
t("ContactPage");
t("Web page type: Contact page.");

// https://schema.org/ContactPoint
t("ContactPoint");
t("A contact point&#x2014;for example, a Customer Complaints department.");

// https://schema.org/ContactPointOption
t("ContactPointOption");
t("Enumerated options related to a ContactPoint.");

// https://schema.org/ContagiousnessHealthAspect
t("ContagiousnessHealthAspect");
t("Content about contagion mechanisms and contagiousness information over the topic.");

// https://schema.org/Continent
t("Continent");
t("One of the continents (for example, Europe or Africa).");

// https://schema.org/ControlAction
t("ControlAction");
t("An agent controls a device or application.");

// https://schema.org/ConvenienceStore
t("ConvenienceStore");
t("A convenience store.");

// https://schema.org/Conversation
t("Conversation");
t("One or more messages between organizations or people on a particular topic. Individual messages can be linked to the conversation with isPartOf or hasPart properties.");
t("One or more messages between organizations or people on a particular topic.");

// https://schema.org/CookAction
t("CookAction");
t("The act of producing/preparing food.");

// https://schema.org/CoOp
t("CoOp");
t("Play mode: CoOp. Co-operative games, where you play on the same team with friends.");
t("Play mode: CoOp.");

// https://schema.org/Corporation
t("Corporation");
t("Organization: A business corporation.");

// https://schema.org/CorrectionComment
t("CorrectionComment");
t('A <a class="localLink" href="/comment">comment</a> that corrects <a class="localLink" href="/CreativeWork">CreativeWork</a>.');

// https://schema.org/Country
t("Country");
t("A country.");

// https://schema.org/Course
t("Course");
t("A description of an educational course which may be offered as distinct instances which take place at different times or take place at different locations, or be offered through different media or modes of study. An educational course is a sequence of one or more educational events and/or creative works which aims to build knowledge, competence or ability of learners.");
t("A description of an educational course which may be offered as distinct instances which take place at different times or take place at different locations, or be offered through different media or modes of study.");

// https://schema.org/CourseInstance
t("CourseInstance");
t('An instance of a <a class="localLink" href="/Course">Course</a> which is distinct from other instances because it is offered at a different time or location or through different media or modes of study or to a specific section of students.');

// https://schema.org/Courthouse
t("Courthouse");
t("A courthouse.");

// https://schema.org/CoverArt
t("CoverArt");
t("The artwork on the outer surface of a CreativeWork.");

// https://schema.org/CovidTestingFacility
t("CovidTestingFacility");
t('A CovidTestingFacility is a <a class="localLink" href="/MedicalClinic">MedicalClinic</a> where testing for the COVID-19 Coronavirus
      disease is available. If the facility is being made available from an established <a class="localLink" href="/Pharmacy">Pharmacy</a>, <a class="localLink" href="/Hotel">Hotel</a>, or other
      non-medical organization, multiple types can be listed. This makes it easier to re-use existing schema.org information
      about that place, e.g. contact info, address, opening hours. Note that in an emergency, such information may not always be reliable.');
t('A CovidTestingFacility is a <a class="localLink" href="/MedicalClinic">MedicalClinic</a> where testing for the COVID-19 Coronavirus
      disease is available.');

// https://schema.org/CreateAction
t("CreateAction");
t("The act of deliberately creating/producing/generating/building a result out of the agent.");

// https://schema.org/CreativeWork
t("CreativeWork");
t("The most generic kind of creative work, including books, movies, photographs, software programs, etc.");

// https://schema.org/CreativeWorkSeason
t("CreativeWorkSeason");
t("A media season, e.g. TV, radio, video game etc.");

// https://schema.org/CreativeWorkSeries
t("CreativeWorkSeries");
t('A CreativeWorkSeries in schema.org is a group of related items, typically but not necessarily of the same kind. CreativeWorkSeries are usually organized into some order, often chronological. Unlike <a class="localLink" href="/ItemList">ItemList</a> which is a general purpose data structure for lists of things, the emphasis with CreativeWorkSeries is on published materials (written e.g. books and periodicals, or media such as TV, radio and games).<br/><br/>

Specific subtypes are available for describing <a class="localLink" href="/TVSeries">TVSeries</a>, <a class="localLink" href="/RadioSeries">RadioSeries</a>, <a class="localLink" href="/MovieSeries">MovieSeries</a>, <a class="localLink" href="/BookSeries">BookSeries</a>, <a class="localLink" href="/Periodical">Periodical</a> and <a class="localLink" href="/VideoGameSeries">VideoGameSeries</a>. In each case, the <a class="localLink" href="/hasPart">hasPart</a> / <a class="localLink" href="/isPartOf">isPartOf</a> properties can be used to relate the CreativeWorkSeries to its parts. The general CreativeWorkSeries type serves largely just to organize these more specific and practical subtypes.<br/><br/>

It is common for properties applicable to an item from the series to be usefully applied to the containing group. Schema.org attempts to anticipate some of these cases, but publishers should be free to apply properties of the series parts to the series as a whole wherever they seem appropriate.');
t("A CreativeWorkSeries in schema.org is a group of related items, typically but not necessarily of the same kind.");

// https://schema.org/CreditCard
t("CreditCard");
t("A card payment method of a particular brand or name.  Used to mark up a particular payment method and/or the financial product/service that supplies the card account.<br/><br/>

Commonly used values:<br/><br/>

<ul>
<li>http://purl.org/goodrelations/v1#AmericanExpress</li>
<li>http://purl.org/goodrelations/v1#DinersClub</li>
<li>http://purl.org/goodrelations/v1#Discover</li>
<li>http://purl.org/goodrelations/v1#JCB</li>
<li>http://purl.org/goodrelations/v1#MasterCard</li>
<li>http://purl.org/goodrelations/v1#VISA</li>
</ul>");
t("A card payment method of a particular brand or name.");

// https://schema.org/Crematorium
t("Crematorium");
t("A crematorium.");

// https://schema.org/CriticReview
t("CriticReview");
t('A <a class="localLink" href="/CriticReview">CriticReview</a> is a more specialized form of Review written or published by a source that is recognized for its reviewing activities. These can include online columns, travel and food guides, TV and radio shows, blogs and other independent Web sites. <a class="localLink" href="/CriticReview">CriticReview</a>s are typically more in-depth and professionally written. For simpler, casually written user/visitor/viewer/customer reviews, it is more appropriate to use the <a class="localLink" href="/UserReview">UserReview</a> type. Review aggregator sites such as Metacritic already separate out the site\'s user reviews from selected critic reviews that originate from third-party sources.');
t('A <a class="localLink" href="/CriticReview">CriticReview</a> is a more specialized form of Review written or published by a source that is recognized for its reviewing activities.');

// https://schema.org/CrossSectional
t("CrossSectional");
t("Studies carried out on pre-existing data (usually from 'snapshot' surveys), such as that collected by the Census Bureau. Sometimes called Prevalence Studies.");
t("Studies carried out on pre-existing data (usually from 'snapshot' surveys), such as that collected by the Census Bureau.");

// https://schema.org/CssSelectorType
t("CssSelectorType");
t("Text representing a CSS selector.");

// https://schema.org/CT
t("CT");
t("X-ray computed tomography imaging.");

// https://schema.org/CurrencyConversionService
t("CurrencyConversionService");
t("A service to convert funds from one currency to another currency.");

// https://schema.org/DamagedCondition
t("DamagedCondition");
t("Indicates that the item is damaged.");

// https://schema.org/DanceEvent
t("DanceEvent");
t("Event type: A social dance.");

// https://schema.org/DanceGroup
t("DanceGroup");
t("A dance group&#x2014;for example, the Alvin Ailey Dance Theater or Riverdance.");

// https://schema.org/DangerousGoodConsideration
t("DangerousGoodConsideration");
t('The item is dangerous and requires careful handling and/or special training of the user. See also the <a href="https://unece.org/DAM/trans/danger/publi/unrec/rev17/English/02EREv17_Part2.pdf">UN Model Classification</a> defining the 9 classes of dangerous goods such as explosives, gases, flammables, and more.');
t("The item is dangerous and requires careful handling and/or special training of the user.");

// https://schema.org/DataCatalog
t("DataCatalog");
t("A collection of datasets.");

// https://schema.org/DataDownload
t("DataDownload");
t('All or part of a <a class="localLink" href="/Dataset">Dataset</a> in downloadable form.');

// https://schema.org/DataDrivenMediaDigitalSource
t("DataDrivenMediaDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/dataDrivenMedia">data driven media</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/DataFeed
t("DataFeed");
t("A single feed providing structured information about one or more entities or topics.");

// https://schema.org/DataFeedItem
t("DataFeedItem");
t("A single item within a larger data feed.");

// https://schema.org/Dataset
t("Dataset");
t("A body of structured information describing some topic(s) of interest.");

// https://schema.org/DataType
t("DataType");
t("The basic data types such as Integers, Strings, etc.");

// https://schema.org/Date
t("Date");
t('A date value in <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 date format</a>.');

// https://schema.org/DatedMoneySpecification
t("DatedMoneySpecification");
t('A DatedMoneySpecification represents monetary values with optional start and end dates. For example, this could represent an employee\'s salary over a specific period of time. <strong>Note:</strong> This type has been superseded by <a class="localLink" href="/MonetaryAmount">MonetaryAmount</a>, use of that type is recommended.');
t("A DatedMoneySpecification represents monetary values with optional start and end dates.");

// https://schema.org/DateTime
t("DateTime");
t("A combination of date and time of day in the form [-]CCYY-MM-DDThh:mm:ss[Z|(+|-)hh:mm] (see Chapter 5.4 of ISO 8601).");

// https://schema.org/DayOfWeek
t("DayOfWeek");
t('The day of the week, e.g. used to specify to which day the opening hours of an OpeningHoursSpecification refer.<br/><br/>

Originally, URLs from <a href="http://purl.org/goodrelations/v1">GoodRelations</a> were used (for <a class="localLink" href="/Monday">Monday</a>, <a class="localLink" href="/Tuesday">Tuesday</a>, <a class="localLink" href="/Wednesday">Wednesday</a>, <a class="localLink" href="/Thursday">Thursday</a>, <a class="localLink" href="/Friday">Friday</a>, <a class="localLink" href="/Saturday">Saturday</a>, <a class="localLink" href="/Sunday">Sunday</a> plus a special entry for <a class="localLink" href="/PublicHolidays">PublicHolidays</a>); these have now been integrated directly into schema.org.');
t("The day of the week, e.g. used to specify to which day the opening hours of an OpeningHoursSpecification refer.");

// https://schema.org/DaySpa
t("DaySpa");
t("A day spa.");

// https://schema.org/DDxElement
t("DDxElement");
t("An alternative, closely-related condition typically considered later in the differential diagnosis process along with the signs that are used to distinguish it.");

// https://schema.org/DeactivateAction
t("DeactivateAction");
t("The act of stopping or deactivating a device or application (e.g. stopping a timer or turning off a flashlight).");

// https://schema.org/DecontextualizedContent
t("DecontextualizedContent");
t('Content coded \'missing context\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.<br/><br/>

For a <a class="localLink" href="/VideoObject">VideoObject</a> to be \'missing context\': Presenting unaltered video in an inaccurate manner that misrepresents the footage. For example, using incorrect dates or locations, altering the transcript or sharing brief clips from a longer video to mislead viewers. (A video rated \'original\' can also be missing context.)<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> to be \'missing context\': Presenting unaltered images in an inaccurate manner to misrepresent the image and mislead the viewer. For example, a common tactic is using an unaltered image but saying it came from a different time or place. (An image rated \'original\' can also be missing context.)<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> with embedded text to be \'missing context\': An unaltered image presented in an inaccurate manner to misrepresent the image and mislead the viewer. For example, a common tactic is using an unaltered image but saying it came from a different time or place. (An \'original\' image with inaccurate text would generally fall in this category.)<br/><br/>

For an <a class="localLink" href="/AudioObject">AudioObject</a> to be \'missing context\': Unaltered audio presented in an inaccurate manner that misrepresents it. For example, using incorrect dates or locations, or sharing brief clips from a longer recording to mislead viewers. (Audio rated “original” can also be missing context.)');
t('Content coded \'missing context\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.');

// https://schema.org/DefenceEstablishment
t("DefenceEstablishment");
t("A defence establishment, such as an army or navy base.");

// https://schema.org/DefinedRegion
t("DefinedRegion");
t('A DefinedRegion is a geographic area defined by potentially arbitrary (rather than political, administrative or natural geographical) criteria. Properties are provided for defining a region by reference to sets of postal codes.<br/><br/>

Examples: a delivery destination when shopping. Region where regional pricing is configured.<br/><br/>

Requirement 1:
Country: US
States: "NY", "CA"<br/><br/>

Requirement 2:
Country: US
PostalCode Set: { [94000-94585], [97000, 97999], [13000, 13599]}
{ [12345, 12345], [78945, 78945], }
Region = state, canton, prefecture, autonomous community...');
t("A DefinedRegion is a geographic area defined by potentially arbitrary (rather than political, administrative or natural geographical) criteria.");

// https://schema.org/DefinedTerm
t("DefinedTerm");
t("A word, name, acronym, phrase, etc. with a formal definition. Often used in the context of category or subject classification, glossaries or dictionaries, product or creative work types, etc. Use the name property for the term being defined, use termCode if the term has an alpha-numeric code allocated, use description to provide the definition of the term.");
t("A word, name, acronym, phrase, etc. with a formal definition.");

// https://schema.org/DefinedTermSet
t("DefinedTermSet");
t("A set of defined terms, for example a set of categories or a classification scheme, a glossary, dictionary or enumeration.");

// https://schema.org/DefinitiveLegalValue
t("DefinitiveLegalValue");
t('Indicates a document for which the text is conclusively what the law says and is legally binding. (E.g. the digitally signed version of an Official Journal.)
  Something "Definitive" is considered to be also <a class="localLink" href="/AuthoritativeLegalValue">AuthoritativeLegalValue</a>.');
t("Indicates a document for which the text is conclusively what the law says and is legally binding.");

// https://schema.org/DeleteAction
t("DeleteAction");
t("The act of editing a recipient by removing one of its objects.");

// https://schema.org/DeliveryChargeSpecification
t("DeliveryChargeSpecification");
t("The price for the delivery of an offer using a particular delivery method.");

// https://schema.org/DeliveryEvent
t("DeliveryEvent");
t("An event involving the delivery of an item.");

// https://schema.org/DeliveryMethod
t("DeliveryMethod");
t("A delivery method is a standardized procedure for transferring the product or service to the destination of fulfillment chosen by the customer. Delivery methods are characterized by the means of transportation used, and by the organization or group that is the contracting party for the sending organization or person.<br/><br/>

Commonly used values:<br/><br/>

<ul>
<li>http://purl.org/goodrelations/v1#DeliveryModeDirectDownload</li>
<li>http://purl.org/goodrelations/v1#DeliveryModeFreight</li>
<li>http://purl.org/goodrelations/v1#DeliveryModeMail</li>
<li>http://purl.org/goodrelations/v1#DeliveryModeOwnFleet</li>
<li>http://purl.org/goodrelations/v1#DeliveryModePickUp</li>
<li>http://purl.org/goodrelations/v1#DHL</li>
<li>http://purl.org/goodrelations/v1#FederalExpress</li>
<li>http://purl.org/goodrelations/v1#UPS</li>
</ul>");
t("A delivery method is a standardized procedure for transferring the product or service to the destination of fulfillment chosen by the customer.");

// https://schema.org/DeliveryTimeSettings
t("DeliveryTimeSettings");
t('A DeliveryTimeSettings represents re-usable pieces of shipping information, relating to timing. It is designed for publication on an URL that may be referenced via the <a class="localLink" href="/shippingSettingsLink">shippingSettingsLink</a> property of an <a class="localLink" href="/OfferShippingDetails">OfferShippingDetails</a>. Several occurrences can be published, distinguished (and identified/referenced) by their different values for <a class="localLink" href="/transitTimeLabel">transitTimeLabel</a>.');
t("A DeliveryTimeSettings represents re-usable pieces of shipping information, relating to timing.");

// https://schema.org/Demand
t("Demand");
t("A demand entity represents the public, not necessarily binding, not necessarily exclusive, announcement by an organization or person to seek a certain type of goods or services. For describing demand using this type, the very same properties used for Offer apply.");
t("A demand entity represents the public, not necessarily binding, not necessarily exclusive, announcement by an organization or person to seek a certain type of goods or services.");

// https://schema.org/DemoAlbum
t("DemoAlbum");
t("DemoAlbum.");

// https://schema.org/DemoGameAvailability
t("DemoGameAvailability");
t("Indicates demo game availability, i.e. a somehow limited demonstration of the full game.");

// https://schema.org/Dentist
t("Dentist");
t("A dentist.");

// https://schema.org/Dentistry
t("Dentistry");
t("A branch of medicine that is involved in the dental care.");

// https://schema.org/DepartAction
t("DepartAction");
t("The act of  departing from a place. An agent departs from a fromLocation for a destination, optionally with participants.");
t("The act of  departing from a place.");

// https://schema.org/DepartmentStore
t("DepartmentStore");
t("A department store.");

// https://schema.org/DepositAccount
t("DepositAccount");
t("A type of Bank Account with a main purpose of depositing funds to gain interest or other benefits.");

// https://schema.org/Dermatologic
t("Dermatologic");
t("Something relating to or practicing dermatology.");

// https://schema.org/Dermatology
t("Dermatology");
t("A specific branch of medical science that pertains to diagnosis and treatment of disorders of skin.");

// https://schema.org/DesktopWebPlatform
t("DesktopWebPlatform");
t("Represents the broad notion of 'desktop' browsers as a Web Platform.");

// https://schema.org/DiabeticDiet
t("DiabeticDiet");
t("A diet appropriate for people with diabetes.");

// https://schema.org/Diagnostic
t("Diagnostic");
t("A medical device used for diagnostic purposes.");

// https://schema.org/DiagnosticLab
t("DiagnosticLab");
t("A medical laboratory that offers on-site or off-site diagnostic services.");

// https://schema.org/DiagnosticProcedure
t("DiagnosticProcedure");
t("A medical procedure intended primarily for diagnostic, as opposed to therapeutic, purposes.");

// https://schema.org/Diet
t("Diet");
t("A strategy of regulating the intake of food to achieve or maintain a specific health-related goal.");

// https://schema.org/DietarySupplement
t("DietarySupplement");
t("A product taken by mouth that contains a dietary ingredient intended to supplement the diet. Dietary ingredients may include vitamins, minerals, herbs or other botanicals, amino acids, and substances such as enzymes, organ tissues, glandulars and metabolites.");
t("A product taken by mouth that contains a dietary ingredient intended to supplement the diet.");

// https://schema.org/DietNutrition
t("DietNutrition");
t("Dietetics and nutrition as a medical specialty.");

// https://schema.org/DigitalArtDigitalSource
t("DigitalArtDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/digitalArt">digital art</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/DigitalAudioTapeFormat
t("DigitalAudioTapeFormat");
t("DigitalAudioTapeFormat.");

// https://schema.org/DigitalCaptureDigitalSource
t("DigitalCaptureDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/digitalCapture">digital capture</a></a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/DigitalDocument
t("DigitalDocument");
t("An electronic file or document.");

// https://schema.org/DigitalDocumentPermission
t("DigitalDocumentPermission");
t("A permission for a particular person or group to access a particular file.");

// https://schema.org/DigitalDocumentPermissionType
t("DigitalDocumentPermissionType");
t("A type of permission which can be granted for accessing a digital document.");

// https://schema.org/DigitalFormat
t("DigitalFormat");
t("DigitalFormat.");

// https://schema.org/DigitalPlatformEnumeration
t("DigitalPlatformEnumeration");
t('Enumerates some common technology platforms, for use with properties such as <a class="localLink" href="/actionPlatform">actionPlatform</a>. It is not supposed to be comprehensive - when a suitable code is not enumerated here, textual or URL values can be used instead. These codes are at a fairly high level and do not deal with versioning and other nuance. Additional codes can be suggested <a href="https://github.com/schemaorg/schemaorg/issues/3057">in github</a>.');
t('Enumerates some common technology platforms, for use with properties such as <a class="localLink" href="/actionPlatform">actionPlatform</a>.');

// https://schema.org/DisabilitySupport
t("DisabilitySupport");
t("DisabilitySupport: this is a benefit for disability support.");

// https://schema.org/DisagreeAction
t("DisagreeAction");
t("The act of expressing a difference of opinion with the object. An agent disagrees to/about an object (a proposition, topic or theme) with participants.");
t("The act of expressing a difference of opinion with the object.");

// https://schema.org/Discontinued
t("Discontinued");
t("Indicates that the item has been discontinued.");

// https://schema.org/DiscoverAction
t("DiscoverAction");
t("The act of discovering/finding an object.");

// https://schema.org/DiscussionForumPosting
t("DiscussionForumPosting");
t("A posting to a discussion forum.");

// https://schema.org/DislikeAction
t("DislikeAction");
t("The act of expressing a negative sentiment about the object. An agent dislikes an object (a proposition, topic or theme) with participants.");
t("The act of expressing a negative sentiment about the object.");

// https://schema.org/Distance
t("Distance");
t("Properties that take Distances as values are of the form '&lt;Number&gt; &lt;Length unit of measure&gt;'. E.g., '7 ft'.");
t("Properties that take Distances as values are of the form '&lt;Number&gt; &lt;Length unit of measure&gt;'.");

// https://schema.org/DistanceFee
t("DistanceFee");
t("Represents the distance fee (e.g., price per km or mile) part of the total price for an offered product, for example a car rental.");

// https://schema.org/Distillery
t("Distillery");
t("A distillery.");

// https://schema.org/DJMixAlbum
t("DJMixAlbum");
t("DJMixAlbum.");

// https://schema.org/DonateAction
t("DonateAction");
t("The act of providing goods, services, or money without compensation, often for philanthropic reasons.");

// https://schema.org/DoseSchedule
t("DoseSchedule");
t("A specific dosing schedule for a drug or supplement.");

// https://schema.org/DoubleBlindedTrial
t("DoubleBlindedTrial");
t("A trial design in which neither the researcher nor the patient knows the details of the treatment the patient was randomly assigned to.");

// https://schema.org/DownloadAction
t("DownloadAction");
t("The act of downloading an object.");

// https://schema.org/Downpayment
t("Downpayment");
t("Represents the downpayment (up-front payment) price component of the total price for an offered product that has additional installment payments.");

// https://schema.org/DrawAction
t("DrawAction");
t("The act of producing a visual/graphical representation of an object, typically with a pen/pencil and paper as instruments.");

// https://schema.org/Drawing
t("Drawing");
t("A picture or diagram made with a pencil, pen, or crayon rather than paint.");

// https://schema.org/DrinkAction
t("DrinkAction");
t("The act of swallowing liquids.");

// https://schema.org/DriveWheelConfigurationValue
t("DriveWheelConfigurationValue");
t("A value indicating which roadwheels will receive torque.");

// https://schema.org/DrivingSchoolVehicleUsage
t("DrivingSchoolVehicleUsage");
t("Indicates the usage of the vehicle for driving school.");

// https://schema.org/Drug
t("Drug");
t("A chemical or biologic substance, used as a medical therapy, that has a physiological effect on an organism. Here the term drug is used interchangeably with the term medicine although clinical knowledge makes a clear difference between them.");
t("A chemical or biologic substance, used as a medical therapy, that has a physiological effect on an organism.");

// https://schema.org/DrugClass
t("DrugClass");
t("A class of medical drugs, e.g., statins. Classes can represent general pharmacological class, common mechanisms of action, common physiological effects, etc.");
t("A class of medical drugs, e.g., statins.");

// https://schema.org/DrugCost
t("DrugCost");
t("The cost per unit of a medical drug. Note that this type is not meant to represent the price in an offer of a drug for sale; see the Offer type for that. This type will typically be used to tag wholesale or average retail cost of a drug, or maximum reimbursable cost. Costs of medical drugs vary widely depending on how and where they are paid for, so while this type captures some of the variables, costs should be used with caution by consumers of this schema's markup.");
t("The cost per unit of a medical drug.");

// https://schema.org/DrugCostCategory
t("DrugCostCategory");
t("Enumerated categories of medical drug costs.");

// https://schema.org/DrugLegalStatus
t("DrugLegalStatus");
t("The legal availability status of a medical drug.");

// https://schema.org/DrugPregnancyCategory
t("DrugPregnancyCategory");
t("Categories that represent an assessment of the risk of fetal injury due to a drug or pharmaceutical used as directed by the mother during pregnancy.");

// https://schema.org/DrugPrescriptionStatus
t("DrugPrescriptionStatus");
t("Indicates whether this drug is available by prescription or over-the-counter.");

// https://schema.org/DrugStrength
t("DrugStrength");
t("A specific strength in which a medical drug is available in a specific country.");

// https://schema.org/DryCleaningOrLaundry
t("DryCleaningOrLaundry");
t("A dry-cleaning business.");

// https://schema.org/Duration
t("Duration");
t('Quantity: Duration (use <a href="http://en.wikipedia.org/wiki/ISO_8601">ISO 8601 duration format</a>).');

// https://schema.org/DVDFormat
t("DVDFormat");
t("DVDFormat.");

// https://schema.org/Ear
t("Ear");
t("Ear function assessment with clinical examination.");

// https://schema.org/EatAction
t("EatAction");
t("The act of swallowing solid objects.");

// https://schema.org/EBook
t("EBook");
t("Book format: Ebook.");

// https://schema.org/EditedOrCroppedContent
t("EditedOrCroppedContent");
t('Content coded \'edited or cropped content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.<br/><br/>

For a <a class="localLink" href="/VideoObject">VideoObject</a> to be \'edited or cropped content\': The video has been edited or rearranged. This category applies to time edits, including editing multiple videos together to alter the story being told or editing out large portions from a video.<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> to be \'edited or cropped content\': Presenting a part of an image from a larger whole to mislead the viewer.<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> with embedded text to be \'edited or cropped content\': Presenting a part of an image from a larger whole to mislead the viewer.<br/><br/>

For an <a class="localLink" href="/AudioObject">AudioObject</a> to be \'edited or cropped content\': The audio has been edited or rearranged. This category applies to time edits, including editing multiple audio clips together to alter the story being told or editing out large portions from the recording.');
t('Content coded \'edited or cropped content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.');

// https://schema.org/EducationalAudience
t("EducationalAudience");
t("An EducationalAudience.");

// https://schema.org/EducationalOccupationalCredential
t("EducationalOccupationalCredential");
t("An educational or occupational credential. A diploma, academic degree, certification, qualification, badge, etc., that may be awarded to a person or other entity that meets the requirements defined by the credentialer.");
t("An educational or occupational credential.");

// https://schema.org/EducationalOccupationalProgram
t("EducationalOccupationalProgram");
t("A program offered by an institution which determines the learning progress to achieve an outcome, usually a credential like a degree or certificate. This would define a discrete set of opportunities (e.g., job, courses) that together constitute a program with a clear start, end, set of requirements, and transition to a new occupational opportunity (e.g., a job), or sometimes a higher educational opportunity (e.g., an advanced degree).");
t("A program offered by an institution which determines the learning progress to achieve an outcome, usually a credential like a degree or certificate.");

// https://schema.org/EducationalOrganization
t("EducationalOrganization");
t("An educational organization.");

// https://schema.org/EducationEvent
t("EducationEvent");
t("Event type: Education event.");

// https://schema.org/EffectivenessHealthAspect
t("EffectivenessHealthAspect");
t("Content about the effectiveness-related aspects of a health topic.");

// https://schema.org/Electrician
t("Electrician");
t("An electrician.");

// https://schema.org/ElectronicsStore
t("ElectronicsStore");
t("An electronics store.");

// https://schema.org/ElementarySchool
t("ElementarySchool");
t("An elementary school.");

// https://schema.org/EmailMessage
t("EmailMessage");
t("An email message.");

// https://schema.org/Embassy
t("Embassy");
t("An embassy.");

// https://schema.org/Emergency
t("Emergency");
t("A specific branch of medical science that deals with the evaluation and initial treatment of medical conditions caused by trauma or sudden illness.");

// https://schema.org/EmergencyService
t("EmergencyService");
t("An emergency service, such as a fire station or ER.");

// https://schema.org/EmployeeRole
t("EmployeeRole");
t("A subclass of OrganizationRole used to describe employee relationships.");

// https://schema.org/EmployerAggregateRating
t("EmployerAggregateRating");
t("An aggregate rating of an Organization related to its role as an employer.");

// https://schema.org/EmployerReview
t("EmployerReview");
t('An <a class="localLink" href="/EmployerReview">EmployerReview</a> is a review of an <a class="localLink" href="/Organization">Organization</a> regarding its role as an employer, written by a current or former employee of that organization.');

// https://schema.org/EmploymentAgency
t("EmploymentAgency");
t("An employment agency.");

// https://schema.org/Endocrine
t("Endocrine");
t("A specific branch of medical science that pertains to diagnosis and treatment of disorders of endocrine glands and their secretions.");

// https://schema.org/EndorseAction
t("EndorseAction");
t("An agent approves/certifies/likes/supports/sanctions an object.");

// https://schema.org/EndorsementRating
t("EndorsementRating");
t('An EndorsementRating is a rating that expresses some level of endorsement, for example inclusion in a "critic\'s pick" blog, a
"Like" or "+1" on a social network. It can be considered the <a class="localLink" href="/result">result</a> of an <a class="localLink" href="/EndorseAction">EndorseAction</a> in which the <a class="localLink" href="/object">object</a> of the action is rated positively by
some <a class="localLink" href="/agent">agent</a>. As is common elsewhere in schema.org, it is sometimes more useful to describe the results of such an action without explicitly describing the <a class="localLink" href="/Action">Action</a>.<br/><br/>

An <a class="localLink" href="/EndorsementRating">EndorsementRating</a> may be part of a numeric scale or organized system, but this is not required: having an explicit type for indicating a positive,
endorsement rating is particularly useful in the absence of numeric scales as it helps consumers understand that the rating is broadly positive.');
t('An EndorsementRating is a rating that expresses some level of endorsement, for example inclusion in a "critic\'s pick" blog, a
"Like" or "+1" on a social network.');

// https://schema.org/Energy
t("Energy");
t("Properties that take Energy as values are of the form '&lt;Number&gt; &lt;Energy unit of measure&gt;'.");

// https://schema.org/EnergyConsumptionDetails
t("EnergyConsumptionDetails");
t('EnergyConsumptionDetails represents information related to the energy efficiency of a product that consumes energy. The information that can be provided is based on international regulations such as for example <a href="https://eur-lex.europa.eu/eli/reg/2017/1369/oj">EU directive 2017/1369</a> for energy labeling and the <a href="https://www.ftc.gov/enforcement/rules/rulemaking-regulatory-reform-proceedings/energy-water-use-labeling-consumer">Energy labeling rule</a> under the Energy Policy and Conservation Act (EPCA) in the US.');
t("EnergyConsumptionDetails represents information related to the energy efficiency of a product that consumes energy.");

// https://schema.org/EnergyEfficiencyEnumeration
t("EnergyEfficiencyEnumeration");
t('Enumerates energy efficiency levels (also known as "classes" or "ratings") and certifications that are part of several international energy efficiency standards.');

// https://schema.org/EnergyStarCertified
t("EnergyStarCertified");
t("Represents EnergyStar certification.");

// https://schema.org/EnergyStarEnergyEfficiencyEnumeration
t("EnergyStarEnergyEfficiencyEnumeration");
t("Used to indicate whether a product is EnergyStar certified.");

// https://schema.org/EngineSpecification
t("EngineSpecification");
t("Information about the engine of the vehicle. A vehicle can have multiple engines represented by multiple engine specification entities.");
t("Information about the engine of the vehicle.");

// https://schema.org/EnrollingByInvitation
t("EnrollingByInvitation");
t("Enrolling participants by invitation only.");

// https://schema.org/EntertainmentBusiness
t("EntertainmentBusiness");
t("A business providing entertainment.");

// https://schema.org/EntryPoint
t("EntryPoint");
t("An entry point, within some Web-based protocol.");

// https://schema.org/Enumeration
t("Enumeration");
t("Lists or enumerations—for example, a list of cuisines or music genres, etc.");

// https://schema.org/Episode
t("Episode");
t("A media episode (e.g. TV, radio, video game) which can be part of a series or season.");

// https://schema.org/EPRelease
t("EPRelease");
t("EPRelease.");

// https://schema.org/EUEnergyEfficiencyCategoryA
t("EUEnergyEfficiencyCategoryA");
t("Represents EU Energy Efficiency Class A as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryA1Plus
t("EUEnergyEfficiencyCategoryA1Plus");
t("Represents EU Energy Efficiency Class A+ as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryA2Plus
t("EUEnergyEfficiencyCategoryA2Plus");
t("Represents EU Energy Efficiency Class A++ as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryA3Plus
t("EUEnergyEfficiencyCategoryA3Plus");
t("Represents EU Energy Efficiency Class A+++ as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryB
t("EUEnergyEfficiencyCategoryB");
t("Represents EU Energy Efficiency Class B as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryC
t("EUEnergyEfficiencyCategoryC");
t("Represents EU Energy Efficiency Class C as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryD
t("EUEnergyEfficiencyCategoryD");
t("Represents EU Energy Efficiency Class D as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryE
t("EUEnergyEfficiencyCategoryE");
t("Represents EU Energy Efficiency Class E as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryF
t("EUEnergyEfficiencyCategoryF");
t("Represents EU Energy Efficiency Class F as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyCategoryG
t("EUEnergyEfficiencyCategoryG");
t("Represents EU Energy Efficiency Class G as defined in EU energy labeling regulations.");

// https://schema.org/EUEnergyEfficiencyEnumeration
t("EUEnergyEfficiencyEnumeration");
t("Enumerates the EU energy efficiency classes A-G as well as A+, A++, and A+++ as defined in EU directive 2017/1369.");

// https://schema.org/Event
t("Event");
t('An event happening at a certain time and location, such as a concert, lecture, or festival. Ticketing information may be added via the <a class="localLink" href="/offers">offers</a> property. Repeated events may be structured as separate Event objects.');
t("An event happening at a certain time and location, such as a concert, lecture, or festival.");

// https://schema.org/EventAttendanceModeEnumeration
t("EventAttendanceModeEnumeration");
t("An EventAttendanceModeEnumeration value is one of potentially several modes of organising an event, relating to whether it is online or offline.");

// https://schema.org/EventCancelled
t("EventCancelled");
t("The event has been cancelled. If the event has multiple startDate values, all are assumed to be cancelled. Either startDate or previousStartDate may be used to specify the event's cancelled date(s).");
t("The event has been cancelled.");

// https://schema.org/EventMovedOnline
t("EventMovedOnline");
t('Indicates that the event was changed to allow online participation. See <a class="localLink" href="/eventAttendanceMode">eventAttendanceMode</a> for specifics of whether it is now fully or partially online.');
t("Indicates that the event was changed to allow online participation.");

// https://schema.org/EventPostponed
t("EventPostponed");
t("The event has been postponed and no new date has been set. The event's previousStartDate should be set.");
t("The event has been postponed and no new date has been set.");

// https://schema.org/EventRescheduled
t("EventRescheduled");
t("The event has been rescheduled. The event's previousStartDate should be set to the old date and the startDate should be set to the event's new date. (If the event has been rescheduled multiple times, the previousStartDate property may be repeated.)");
t("The event has been rescheduled.");

// https://schema.org/EventReservation
t("EventReservation");
t('A reservation for an event like a concert, sporting event, or lecture.<br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations. For offers of tickets, use <a class="localLink" href="/Offer">Offer</a>.');
t("A reservation for an event like a concert, sporting event, or lecture.");

// https://schema.org/EventScheduled
t("EventScheduled");
t("The event is taking place or has taken place on the startDate as scheduled. Use of this value is optional, as it is assumed by default.");
t("The event is taking place or has taken place on the startDate as scheduled.");

// https://schema.org/EventSeries
t("EventSeries");
t('A series of <a class="localLink" href="/Event">Event</a>s. Included events can relate with the series using the <a class="localLink" href="/superEvent">superEvent</a> property.<br/><br/>

An EventSeries is a collection of events that share some unifying characteristic. For example, "The Olympic Games" is a series, which
is repeated regularly. The "2012 London Olympics" can be presented both as an <a class="localLink" href="/Event">Event</a> in the series "Olympic Games", and as an
<a class="localLink" href="/EventSeries">EventSeries</a> that included a number of sporting competitions as Events.<br/><br/>

The nature of the association between the events in an <a class="localLink" href="/EventSeries">EventSeries</a> can vary, but typical examples could
include a thematic event series (e.g. topical meetups or classes), or a series of regular events that share a location, attendee group and/or organizers.<br/><br/>

EventSeries has been defined as a kind of Event to make it easy for publishers to use it in an Event context without
worrying about which kinds of series are really event-like enough to call an Event. In general an EventSeries
may seem more Event-like when the period of time is compact and when aspects such as location are fixed, but
it may also sometimes prove useful to describe a longer-term series as an Event.');
t('A series of <a class="localLink" href="/Event">Event</a>s.');

// https://schema.org/EventStatusType
t("EventStatusType");
t("EventStatusType is an enumeration type whose instances represent several states that an Event may be in.");

// https://schema.org/EventVenue
t("EventVenue");
t("An event venue.");

// https://schema.org/EvidenceLevelA
t("EvidenceLevelA");
t("Data derived from multiple randomized clinical trials or meta-analyses.");

// https://schema.org/EvidenceLevelB
t("EvidenceLevelB");
t("Data derived from a single randomized trial, or nonrandomized studies.");

// https://schema.org/EvidenceLevelC
t("EvidenceLevelC");
t("Only consensus opinion of experts, case studies, or standard-of-care.");

// https://schema.org/ExampleMeasurementMethodEnum
t("ExampleMeasurementMethodEnum");
t('An example <a class="localLink" href="/MeasurementMethodEnum">MeasurementMethodEnum</a> (to remove when real enums are added).');

// https://schema.org/ExchangeRateSpecification
t("ExchangeRateSpecification");
t("A structured value representing exchange rate.");

// https://schema.org/ExchangeRefund
t("ExchangeRefund");
t("Specifies that a refund can be done as an exchange for the same product.");

// https://schema.org/ExerciseAction
t("ExerciseAction");
t("The act of participating in exertive activity for the purposes of improving health and fitness.");

// https://schema.org/ExerciseGym
t("ExerciseGym");
t("A gym.");

// https://schema.org/ExercisePlan
t("ExercisePlan");
t("Fitness-related activity designed for a specific health-related purpose, including defined exercise routines as well as activity prescribed by a clinician.");

// https://schema.org/ExhibitionEvent
t("ExhibitionEvent");
t("Event type: Exhibition event, e.g. at a museum, library, archive, tradeshow, ...");

// https://schema.org/Eye
t("Eye");
t("Eye or ophthalmological function assessment with clinical examination.");

// https://schema.org/FailedActionStatus
t("FailedActionStatus");
t("An action that failed to complete. The action's error property and the HTTP return code contain more information about the failure.");
t("An action that failed to complete.");

// https://schema.org/False
t("False");
t("The boolean value false.");

// https://schema.org/FAQPage
t("FAQPage");
t('A <a class="localLink" href="/FAQPage">FAQPage</a> is a <a class="localLink" href="/WebPage">WebPage</a> presenting one or more "<a href="https://en.wikipedia.org/wiki/FAQ">Frequently asked questions</a>" (see also <a class="localLink" href="/QAPage">QAPage</a>).');

// https://schema.org/FastFoodRestaurant
t("FastFoodRestaurant");
t("A fast-food restaurant.");

// https://schema.org/FDAcategoryA
t("FDAcategoryA");
t("A designation by the US FDA signifying that adequate and well-controlled studies have failed to demonstrate a risk to the fetus in the first trimester of pregnancy (and there is no evidence of risk in later trimesters).");

// https://schema.org/FDAcategoryB
t("FDAcategoryB");
t("A designation by the US FDA signifying that animal reproduction studies have failed to demonstrate a risk to the fetus and there are no adequate and well-controlled studies in pregnant women.");

// https://schema.org/FDAcategoryC
t("FDAcategoryC");
t("A designation by the US FDA signifying that animal reproduction studies have shown an adverse effect on the fetus and there are no adequate and well-controlled studies in humans, but potential benefits may warrant use of the drug in pregnant women despite potential risks.");

// https://schema.org/FDAcategoryD
t("FDAcategoryD");
t("A designation by the US FDA signifying that there is positive evidence of human fetal risk based on adverse reaction data from investigational or marketing experience or studies in humans, but potential benefits may warrant use of the drug in pregnant women despite potential risks.");

// https://schema.org/FDAcategoryX
t("FDAcategoryX");
t("A designation by the US FDA signifying that studies in animals or humans have demonstrated fetal abnormalities and/or there is positive evidence of human fetal risk based on adverse reaction data from investigational or marketing experience, and the risks involved in use of the drug in pregnant women clearly outweigh potential benefits.");

// https://schema.org/FDAnotEvaluated
t("FDAnotEvaluated");
t("A designation that the drug in question has not been assigned a pregnancy category designation by the US FDA.");

// https://schema.org/Female
t("Female");
t("The female gender.");

// https://schema.org/Festival
t("Festival");
t("Event type: Festival.");

// https://schema.org/FilmAction
t("FilmAction");
t("The act of capturing sound and moving images on film, video, or digitally.");

// https://schema.org/FinancialProduct
t("FinancialProduct");
t("A product provided to consumers and businesses by financial institutions such as banks, insurance companies, brokerage firms, consumer finance companies, and investment companies which comprise the financial services industry.");

// https://schema.org/FinancialService
t("FinancialService");
t("Financial services business.");

// https://schema.org/FindAction
t("FindAction");
t('The act of finding an object.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/SearchAction">SearchAction</a>: FindAction is generally lead by a SearchAction, but not necessarily.</li>
</ul>');
t("The act of finding an object.");

// https://schema.org/FireStation
t("FireStation");
t("A fire station. With firemen.");
t("A fire station.");

// https://schema.org/Flexibility
t("Flexibility");
t("Physical activity that is engaged in to improve joint and muscle flexibility.");

// https://schema.org/Flight
t("Flight");
t("An airline flight.");

// https://schema.org/FlightReservation
t("FlightReservation");
t('A reservation for air travel.<br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations. For offers of tickets, use <a class="localLink" href="/Offer">Offer</a>.');
t("A reservation for air travel.");

// https://schema.org/Float
t("Float");
t("Data type: Floating number.");

// https://schema.org/FloorPlan
t("FloorPlan");
t('A FloorPlan is an explicit representation of a collection of similar accommodations, allowing the provision of common information (room counts, sizes, layout diagrams) and offers for rental or sale. In typical use, some <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a> has an <a class="localLink" href="/accommodationFloorPlan">accommodationFloorPlan</a> which is a <a class="localLink" href="/FloorPlan">FloorPlan</a>.  A FloorPlan is always in the context of a particular place, either a larger <a class="localLink" href="/ApartmentComplex">ApartmentComplex</a> or a single <a class="localLink" href="/Apartment">Apartment</a>. The visual/spatial aspects of a floor plan (i.e. room layout, <a href="https://en.wikipedia.org/wiki/Floor_plan">see wikipedia</a>) can be indicated using <a class="localLink" href="/image">image</a>.');
t("A FloorPlan is an explicit representation of a collection of similar accommodations, allowing the provision of common information (room counts, sizes, layout diagrams) and offers for rental or sale.");

// https://schema.org/Florist
t("Florist");
t("A florist.");

// https://schema.org/FMRadioChannel
t("FMRadioChannel");
t("A radio channel that uses FM.");

// https://schema.org/FollowAction
t("FollowAction");
t('The act of forming a personal connection with someone/something (object) unidirectionally/asymmetrically to get updates polled from.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/BefriendAction">BefriendAction</a>: Unlike BefriendAction, FollowAction implies that the connection is <em>not</em> necessarily reciprocal.</li>
<li><a class="localLink" href="/SubscribeAction">SubscribeAction</a>: Unlike SubscribeAction, FollowAction implies that the follower acts as an active agent constantly/actively polling for updates.</li>
<li><a class="localLink" href="/RegisterAction">RegisterAction</a>: Unlike RegisterAction, FollowAction implies that the agent is interested in continuing receiving updates from the object.</li>
<li><a class="localLink" href="/JoinAction">JoinAction</a>: Unlike JoinAction, FollowAction implies that the agent is interested in getting updates from the object.</li>
<li><a class="localLink" href="/TrackAction">TrackAction</a>: Unlike TrackAction, FollowAction refers to the polling of updates of all aspects of animate objects rather than the location of inanimate objects (e.g. you track a package, but you don\'t follow it).</li>
</ul>');
t("The act of forming a personal connection with someone/something (object) unidirectionally/asymmetrically to get updates polled from.");

// https://schema.org/FoodEstablishment
t("FoodEstablishment");
t("A food-related business.");

// https://schema.org/FoodEstablishmentReservation
t("FoodEstablishmentReservation");
t("A reservation to dine at a food-related business.<br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.");
t("A reservation to dine at a food-related business.");

// https://schema.org/FoodEvent
t("FoodEvent");
t("Event type: Food event.");

// https://schema.org/FoodService
t("FoodService");
t("A food service, like breakfast, lunch, or dinner.");

// https://schema.org/FourWheelDriveConfiguration
t("FourWheelDriveConfiguration");
t("Four-wheel drive is a transmission layout where the engine primarily drives two wheels with a part-time four-wheel drive capability.");

// https://schema.org/FreeReturn
t("FreeReturn");
t("Specifies that product returns are free of charge for the customer.");

// https://schema.org/Friday
t("Friday");
t("The day of the week between Thursday and Saturday.");

// https://schema.org/FrontWheelDriveConfiguration
t("FrontWheelDriveConfiguration");
t("Front-wheel drive is a transmission layout where the engine drives the front wheels.");

// https://schema.org/FullGameAvailability
t("FullGameAvailability");
t("Indicates full game availability.");

// https://schema.org/FullRefund
t("FullRefund");
t("Specifies that a refund can be done in the full amount the customer paid for the product.");

// https://schema.org/FundingAgency
t("FundingAgency");
t('A FundingAgency is an organization that implements one or more <a class="localLink" href="/FundingScheme">FundingScheme</a>s and manages
    the granting process (via <a class="localLink" href="/Grant">Grant</a>s, typically <a class="localLink" href="/MonetaryGrant">MonetaryGrant</a>s).
    A funding agency is not always required for grant funding, e.g. philanthropic giving, corporate sponsorship etc.<br/><br/>

Examples of funding agencies include ERC, REA, NIH, Bill and Melinda Gates Foundation, ...');
t('A FundingAgency is an organization that implements one or more <a class="localLink" href="/FundingScheme">FundingScheme</a>s and manages
    the granting process (via <a class="localLink" href="/Grant">Grant</a>s, typically <a class="localLink" href="/MonetaryGrant">MonetaryGrant</a>s).');

// https://schema.org/FundingScheme
t("FundingScheme");
t('A FundingScheme combines organizational, project and policy aspects of grant-based funding
    that sets guidelines, principles and mechanisms to support other kinds of projects and activities.
    Funding is typically organized via <a class="localLink" href="/Grant">Grant</a> funding. Examples of funding schemes: Swiss Priority Programmes (SPPs); EU Framework 7 (FP7); Horizon 2020; the NIH-R01 Grant Program; Wellcome institutional strategic support fund. For large scale public sector funding, the management and administration of grant awards is often handled by other, dedicated, organizations - <a class="localLink" href="/FundingAgency">FundingAgency</a>s such as ERC, REA, ...');
t("A FundingScheme combines organizational, project and policy aspects of grant-based funding
    that sets guidelines, principles and mechanisms to support other kinds of projects and activities.");

// https://schema.org/Fungus
t("Fungus");
t("Pathogenic fungus.");

// https://schema.org/FurnitureStore
t("FurnitureStore");
t("A furniture store.");

// https://schema.org/Game
t("Game");
t("The Game type represents things which are games. These are typically rule-governed recreational activities, e.g. role-playing games in which players assume the role of characters in a fictional setting.");
t("The Game type represents things which are games.");

// https://schema.org/GameAvailabilityEnumeration
t("GameAvailabilityEnumeration");
t('For a <a class="localLink" href="/VideoGame">VideoGame</a>, such as used with a <a class="localLink" href="/PlayGameAction">PlayGameAction</a>, an enumeration of the kind of game availability offered.');

// https://schema.org/GamePlayMode
t("GamePlayMode");
t("Indicates whether this game is multi-player, co-op or single-player.");

// https://schema.org/GameServer
t("GameServer");
t("Server that provides game interaction in a multiplayer game.");

// https://schema.org/GameServerStatus
t("GameServerStatus");
t("Status of a game server.");

// https://schema.org/GardenStore
t("GardenStore");
t("A garden store.");

// https://schema.org/GasStation
t("GasStation");
t("A gas station.");

// https://schema.org/Gastroenterologic
t("Gastroenterologic");
t("A specific branch of medical science that pertains to diagnosis and treatment of disorders of digestive system.");

// https://schema.org/GatedResidenceCommunity
t("GatedResidenceCommunity");
t("Residence type: Gated community.");

// https://schema.org/GenderType
t("GenderType");
t("An enumeration of genders.");

// https://schema.org/Gene
t("Gene");
t('A discrete unit of inheritance which affects one or more biological traits (Source: <a href="https://en.wikipedia.org/wiki/Gene">https://en.wikipedia.org/wiki/Gene</a>). Examples include FOXP2 (Forkhead box protein P2), SCARNA21 (small Cajal body-specific RNA 21), A- (agouti genotype).');
t('A discrete unit of inheritance which affects one or more biological traits (Source: <a href="https://en.wikipedia.org/wiki/Gene">https://en.wikipedia.org/wiki/Gene</a>).');

// https://schema.org/GeneralContractor
t("GeneralContractor");
t("A general contractor.");

// https://schema.org/GenericWebPlatform
t("GenericWebPlatform");
t('Represents the generic notion of the Web Platform. More specific codes include <a class="localLink" href="/MobileWebPlatform">MobileWebPlatform</a> and <a class="localLink" href="/DesktopWebPlatform">DesktopWebPlatform</a>, as an incomplete list.');
t("Represents the generic notion of the Web Platform.");

// https://schema.org/Genetic
t("Genetic");
t("A specific branch of medical science that pertains to hereditary transmission and the variation of inherited characteristics and disorders.");

// https://schema.org/Genitourinary
t("Genitourinary");
t("Genitourinary system function assessment with clinical examination.");

// https://schema.org/GeoCircle
t("GeoCircle");
t("A GeoCircle is a GeoShape representing a circular geographic area. As it is a GeoShape
          it provides the simple textual property 'circle', but also allows the combination of postalCode alongside geoRadius.
          The center of the circle can be indicated via the 'geoMidpoint' property, or more approximately using 'address', 'postalCode'.");
t("A GeoCircle is a GeoShape representing a circular geographic area.");

// https://schema.org/GeoCoordinates
t("GeoCoordinates");
t("The geographic coordinates of a place or event.");

// https://schema.org/GeoShape
t("GeoShape");
t("The geographic shape of a place. A GeoShape can be described using several properties whose values are based on latitude/longitude pairs. Either whitespace or commas can be used to separate latitude and longitude; whitespace should be used when writing a list of several such points.");
t("The geographic shape of a place.");

// https://schema.org/GeospatialGeometry
t("GeospatialGeometry");
t("(Eventually to be defined as) a supertype of GeoShape designed to accommodate definitions from Geo-Spatial best practices.");

// https://schema.org/Geriatric
t("Geriatric");
t("A specific branch of medical science that is concerned with the diagnosis and treatment of diseases, debilities and provision of care to the aged.");

// https://schema.org/GettingAccessHealthAspect
t("GettingAccessHealthAspect");
t("Content that discusses practical and policy aspects for getting access to specific kinds of healthcare (e.g. distribution mechanisms for vaccines).");

// https://schema.org/GiveAction
t("GiveAction");
t('The act of transferring ownership of an object to a destination. Reciprocal of TakeAction.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/TakeAction">TakeAction</a>: Reciprocal of GiveAction.</li>
<li><a class="localLink" href="/SendAction">SendAction</a>: Unlike SendAction, GiveAction implies that ownership is being transferred (e.g. I may send my laptop to you, but that doesn\'t mean I\'m giving it to you).</li>
</ul>');
t("The act of transferring ownership of an object to a destination.");

// https://schema.org/GlutenFreeDiet
t("GlutenFreeDiet");
t("A diet exclusive of gluten.");

// https://schema.org/GolfCourse
t("GolfCourse");
t("A golf course.");

// https://schema.org/GovernmentBenefitsType
t("GovernmentBenefitsType");
t("GovernmentBenefitsType enumerates several kinds of government benefits to support the COVID-19 situation. Note that this structure may not capture all benefits offered.");
t("GovernmentBenefitsType enumerates several kinds of government benefits to support the COVID-19 situation.");

// https://schema.org/GovernmentBuilding
t("GovernmentBuilding");
t("A government building.");

// https://schema.org/GovernmentOffice
t("GovernmentOffice");
t("A government office&#x2014;for example, an IRS or DMV office.");

// https://schema.org/GovernmentOrganization
t("GovernmentOrganization");
t("A governmental organization or agency.");

// https://schema.org/GovernmentPermit
t("GovernmentPermit");
t("A permit issued by a government agency.");

// https://schema.org/GovernmentService
t("GovernmentService");
t("A service provided by a government organization, e.g. food stamps, veterans benefits, etc.");

// https://schema.org/Grant
t("Grant");
t('A grant, typically financial or otherwise quantifiable, of resources. Typically a <a class="localLink" href="/funder">funder</a> sponsors some <a class="localLink" href="/MonetaryAmount">MonetaryAmount</a> to an <a class="localLink" href="/Organization">Organization</a> or <a class="localLink" href="/Person">Person</a>,
    sometimes not necessarily via a dedicated or long-lived <a class="localLink" href="/Project">Project</a>, resulting in one or more outputs, or <a class="localLink" href="/fundedItem">fundedItem</a>s. For financial sponsorship, indicate the <a class="localLink" href="/funder">funder</a> of a <a class="localLink" href="/MonetaryGrant">MonetaryGrant</a>. For non-financial support, indicate <a class="localLink" href="/sponsor">sponsor</a> of <a class="localLink" href="/Grant">Grant</a>s of resources (e.g. office space).<br/><br/>

Grants support  activities directed towards some agreed collective goals, often but not always organized as <a class="localLink" href="/Project">Project</a>s. Long-lived projects are sometimes sponsored by a variety of grants over time, but it is also common for a project to be associated with a single grant.<br/><br/>

The amount of a <a class="localLink" href="/Grant">Grant</a> is represented using <a class="localLink" href="/amount">amount</a> as a <a class="localLink" href="/MonetaryAmount">MonetaryAmount</a>.');
t("A grant, typically financial or otherwise quantifiable, of resources.");

// https://schema.org/GraphicNovel
t("GraphicNovel");
t("Book format: GraphicNovel. May represent a bound collection of ComicIssue instances.");
t("Book format: GraphicNovel.");

// https://schema.org/GroceryStore
t("GroceryStore");
t("A grocery store.");

// https://schema.org/GroupBoardingPolicy
t("GroupBoardingPolicy");
t("The airline boards by groups based on check-in time, priority, etc.");

// https://schema.org/Guide
t("Guide");
t('<a class="localLink" href="/Guide">Guide</a> is a page or article that recommends specific products or services, or aspects of a thing for a user to consider. A <a class="localLink" href="/Guide">Guide</a> may represent a Buying Guide and detail aspects of products or services for a user to consider. A <a class="localLink" href="/Guide">Guide</a> may represent a Product Guide and recommend specific products or services. A <a class="localLink" href="/Guide">Guide</a> may represent a Ranked List and recommend specific products or services with ranking.');
t('<a class="localLink" href="/Guide">Guide</a> is a page or article that recommends specific products or services, or aspects of a thing for a user to consider.');

// https://schema.org/Gynecologic
t("Gynecologic");
t("A specific branch of medical science that pertains to the health care of women, particularly in the diagnosis and treatment of disorders affecting the female reproductive system.");

// https://schema.org/Hackathon
t("Hackathon");
t('A <a href="https://en.wikipedia.org/wiki/Hackathon">hackathon</a> event.');

// https://schema.org/HairSalon
t("HairSalon");
t("A hair salon.");

// https://schema.org/HalalDiet
t("HalalDiet");
t("A diet conforming to Islamic dietary practices.");

// https://schema.org/Hardcover
t("Hardcover");
t("Book format: Hardcover.");

// https://schema.org/HardwareStore
t("HardwareStore");
t("A hardware store.");

// https://schema.org/Head
t("Head");
t("Head assessment with clinical examination.");

// https://schema.org/HealthAndBeautyBusiness
t("HealthAndBeautyBusiness");
t("Health and beauty.");

// https://schema.org/HealthAspectEnumeration
t("HealthAspectEnumeration");
t('HealthAspectEnumeration enumerates several aspects of health content online, each of which might be described using <a class="localLink" href="/hasHealthAspect">hasHealthAspect</a> and <a class="localLink" href="/HealthTopicContent">HealthTopicContent</a>.');

// https://schema.org/HealthCare
t("HealthCare");
t("HealthCare: this is a benefit for health care.");

// https://schema.org/HealthcareConsideration
t("HealthcareConsideration");
t("Item is a pharmaceutical (e.g., a prescription or OTC drug) or a restricted medical device.");

// https://schema.org/HealthClub
t("HealthClub");
t("A health club.");

// https://schema.org/HealthInsurancePlan
t("HealthInsurancePlan");
t("A US-style health insurance plan, including PPOs, EPOs, and HMOs.");

// https://schema.org/HealthPlanCostSharingSpecification
t("HealthPlanCostSharingSpecification");
t("A description of costs to the patient under a given network or formulary.");

// https://schema.org/HealthPlanFormulary
t("HealthPlanFormulary");
t("For a given health insurance plan, the specification for costs and coverage of prescription drugs.");

// https://schema.org/HealthPlanNetwork
t("HealthPlanNetwork");
t("A US-style health insurance plan network.");

// https://schema.org/HealthTopicContent
t("HealthTopicContent");
t('<a class="localLink" href="/HealthTopicContent">HealthTopicContent</a> is <a class="localLink" href="/WebContent">WebContent</a> that is about some aspect of a health topic, e.g. a condition, its symptoms or treatments. Such content may be comprised of several parts or sections and use different types of media. Multiple instances of <a class="localLink" href="/WebContent">WebContent</a> (and hence <a class="localLink" href="/HealthTopicContent">HealthTopicContent</a>) can be related using <a class="localLink" href="/hasPart">hasPart</a> / <a class="localLink" href="/isPartOf">isPartOf</a> where there is some kind of content hierarchy, and their content described with <a class="localLink" href="/about">about</a> and <a class="localLink" href="/mentions">mentions</a> e.g. building upon the existing <a class="localLink" href="/MedicalCondition">MedicalCondition</a> vocabulary.');
t('<a class="localLink" href="/HealthTopicContent">HealthTopicContent</a> is <a class="localLink" href="/WebContent">WebContent</a> that is about some aspect of a health topic, e.g. a condition, its symptoms or treatments.');

// https://schema.org/HearingImpairedSupported
t("HearingImpairedSupported");
t("Uses devices to support users with hearing impairments.");

// https://schema.org/Hematologic
t("Hematologic");
t("A specific branch of medical science that pertains to diagnosis and treatment of disorders of blood and blood producing organs.");

// https://schema.org/HighSchool
t("HighSchool");
t("A high school.");

// https://schema.org/HinduDiet
t("HinduDiet");
t("A diet conforming to Hindu dietary practices, in particular, beef-free.");

// https://schema.org/HinduTemple
t("HinduTemple");
t("A Hindu temple.");

// https://schema.org/HobbyShop
t("HobbyShop");
t("A store that sells materials useful or necessary for various hobbies.");

// https://schema.org/HomeAndConstructionBusiness
t("HomeAndConstructionBusiness");
t('A construction business.<br/><br/>

A HomeAndConstructionBusiness is a <a class="localLink" href="/LocalBusiness">LocalBusiness</a> that provides services around homes and buildings.<br/><br/>

As a <a class="localLink" href="/LocalBusiness">LocalBusiness</a> it can be described as a <a class="localLink" href="/provider">provider</a> of one or more <a class="localLink" href="/Service">Service</a>(s).');
t("A construction business.");

// https://schema.org/HomeGoodsStore
t("HomeGoodsStore");
t("A home goods store.");

// https://schema.org/Homeopathic
t("Homeopathic");
t("A system of medicine based on the principle that a disease can be cured by a substance that produces similar symptoms in healthy people.");

// https://schema.org/Hospital
t("Hospital");
t("A hospital.");

// https://schema.org/Hostel
t("Hostel");
t('A hostel - cheap accommodation, often in shared dormitories.
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("A hostel - cheap accommodation, often in shared dormitories.");

// https://schema.org/Hotel
t("Hotel");
t('A hotel is an establishment that provides lodging paid on a short-term basis (source: Wikipedia, the free encyclopedia, see http://en.wikipedia.org/wiki/Hotel).
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("A hotel is an establishment that provides lodging paid on a short-term basis.");

// https://schema.org/HotelRoom
t("HotelRoom");
t('A hotel room is a single room in a hotel.
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("A hotel room is a single room in a hotel.");

// https://schema.org/House
t("House");
t('A house is a building or structure that has the ability to be occupied for habitation by humans or other creatures (source: Wikipedia, the free encyclopedia, see <a href="http://en.wikipedia.org/wiki/House">http://en.wikipedia.org/wiki/House</a>).');
t("A house is a building or structure that has the ability to be occupied for habitation by humans or other creatures.");

// https://schema.org/HousePainter
t("HousePainter");
t("A house painting service.");

// https://schema.org/HowItWorksHealthAspect
t("HowItWorksHealthAspect");
t("Content that discusses and explains how a particular health-related topic works, e.g. in terms of mechanisms and underlying science.");

// https://schema.org/HowOrWhereHealthAspect
t("HowOrWhereHealthAspect");
t("Information about how or where to find a topic. Also may contain location data that can be used for where to look for help if the topic is observed.");
t("Information about how or where to find a topic.");

// https://schema.org/HowTo
t("HowTo");
t("Instructions that explain how to achieve a result by performing a sequence of steps.");

// https://schema.org/HowToDirection
t("HowToDirection");
t("A direction indicating a single action to do in the instructions for how to achieve a result.");

// https://schema.org/HowToItem
t("HowToItem");
t("An item used as either a tool or supply when performing the instructions for how to achieve a result.");

// https://schema.org/HowToSection
t("HowToSection");
t("A sub-grouping of steps in the instructions for how to achieve a result (e.g. steps for making a pie crust within a pie recipe).");

// https://schema.org/HowToStep
t("HowToStep");
t("A step in the instructions for how to achieve a result. It is an ordered list with HowToDirection and/or HowToTip items.");
t("A step in the instructions for how to achieve a result.");

// https://schema.org/HowToSupply
t("HowToSupply");
t("A supply consumed when performing the instructions for how to achieve a result.");

// https://schema.org/HowToTip
t("HowToTip");
t("An explanation in the instructions for how to achieve a result. It provides supplementary information about a technique, supply, author's preference, etc. It can explain what could be done, or what should not be done, but doesn't specify what should be done (see HowToDirection).");
t("An explanation in the instructions for how to achieve a result.");

// https://schema.org/HowToTool
t("HowToTool");
t("A tool used (but not consumed) when performing instructions for how to achieve a result.");

// https://schema.org/HVACBusiness
t("HVACBusiness");
t("A business that provides Heating, Ventilation and Air Conditioning services.");

// https://schema.org/HyperToc
t("HyperToc");
t('A HyperToc represents a hypertext table of contents for complex media objects, such as <a class="localLink" href="/VideoObject">VideoObject</a>, <a class="localLink" href="/AudioObject">AudioObject</a>. Items in the table of contents are indicated using the <a class="localLink" href="/tocEntry">tocEntry</a> property, and typed <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a>. For cases where the same larger work is split into multiple files, <a class="localLink" href="/associatedMedia">associatedMedia</a> can be used on individual <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a> items.');
t('A HyperToc represents a hypertext table of contents for complex media objects, such as <a class="localLink" href="/VideoObject">VideoObject</a>, <a class="localLink" href="/AudioObject">AudioObject</a>.');

// https://schema.org/HyperTocEntry
t("HyperTocEntry");
t('A HyperToEntry is an item within a <a class="localLink" href="/HyperToc">HyperToc</a>, which represents a hypertext table of contents for complex media objects, such as <a class="localLink" href="/VideoObject">VideoObject</a>, <a class="localLink" href="/AudioObject">AudioObject</a>. The media object itself is indicated using <a class="localLink" href="/associatedMedia">associatedMedia</a>. Each section of interest within that content can be described with a <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a>, with associated <a class="localLink" href="/startOffset">startOffset</a> and <a class="localLink" href="/endOffset">endOffset</a>. When several entries are all from the same file, <a class="localLink" href="/associatedMedia">associatedMedia</a> is used on the overarching <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a>; if the content has been split into multiple files, they can be referenced using <a class="localLink" href="/associatedMedia">associatedMedia</a> on each <a class="localLink" href="/HyperTocEntry">HyperTocEntry</a>.');
t('A HyperToEntry is an item within a <a class="localLink" href="/HyperToc">HyperToc</a>, which represents a hypertext table of contents for complex media objects, such as <a class="localLink" href="/VideoObject">VideoObject</a>, <a class="localLink" href="/AudioObject">AudioObject</a>.');

// https://schema.org/IceCreamShop
t("IceCreamShop");
t("An ice cream shop.");

// https://schema.org/IgnoreAction
t("IgnoreAction");
t("The act of intentionally disregarding the object. An agent ignores an object.");
t("The act of intentionally disregarding the object.");

// https://schema.org/ImageGallery
t("ImageGallery");
t("Web page type: Image gallery page.");

// https://schema.org/ImageObject
t("ImageObject");
t("An image file.");

// https://schema.org/ImageObjectSnapshot
t("ImageObjectSnapshot");
t('A specific and exact (byte-for-byte) version of an <a class="localLink" href="/ImageObject">ImageObject</a>. Two byte-for-byte identical files, for the purposes of this type, considered identical. If they have different embedded metadata (e.g. XMP, EXIF) the files will differ. Different external facts about the files, e.g. creator or dateCreated that aren\'t represented in their actual content, do not affect this notion of identity.');
t('A specific and exact (byte-for-byte) version of an <a class="localLink" href="/ImageObject">ImageObject</a>.');

// https://schema.org/ImagingTest
t("ImagingTest");
t("Any medical imaging modality typically used for diagnostic purposes.");

// https://schema.org/IndividualPhysician
t("IndividualPhysician");
t('An individual medical practitioner. For their official address use <a class="localLink" href="/address">address</a>, for affiliations to hospitals use <a class="localLink" href="/hospitalAffiliation">hospitalAffiliation</a>.
The <a class="localLink" href="/practicesAt">practicesAt</a> property can be used to indicate <a class="localLink" href="/MedicalOrganization">MedicalOrganization</a> hospitals, clinics, pharmacies etc. where this physician practices.');
t("An individual medical practitioner.");

// https://schema.org/IndividualProduct
t("IndividualProduct");
t("A single, identifiable product instance (e.g. a laptop with a particular serial number).");

// https://schema.org/Infectious
t("Infectious");
t("Something in medical science that pertains to infectious diseases, i.e. caused by bacterial, viral, fungal or parasitic infections.");

// https://schema.org/InfectiousAgentClass
t("InfectiousAgentClass");
t("Classes of agents or pathogens that transmit infectious diseases. Enumerated type.");
t("Classes of agents or pathogens that transmit infectious diseases.");

// https://schema.org/InfectiousDisease
t("InfectiousDisease");
t("An infectious disease is a clinically evident human disease resulting from the presence of pathogenic microbial agents, like pathogenic viruses, pathogenic bacteria, fungi, protozoa, multicellular parasites, and prions. To be considered an infectious disease, such pathogens are known to be able to cause this disease.");
t("An infectious disease is a clinically evident human disease resulting from the presence of pathogenic microbial agents, like pathogenic viruses, pathogenic bacteria, fungi, protozoa, multicellular parasites, and prions.");

// https://schema.org/InForce
t("InForce");
t("Indicates that a legislation is in force.");

// https://schema.org/InformAction
t("InformAction");
t("The act of notifying someone of information pertinent to them, with no expectation of a response.");

// https://schema.org/IngredientsHealthAspect
t("IngredientsHealthAspect");
t("Content discussing ingredients-related aspects of a health topic.");

// https://schema.org/InsertAction
t("InsertAction");
t("The act of adding at a specific location in an ordered collection.");

// https://schema.org/InstallAction
t("InstallAction");
t("The act of installing an application.");

// https://schema.org/Installment
t("Installment");
t("Represents the installment pricing component of the total price for an offered product.");

// https://schema.org/InStock
t("InStock");
t("Indicates that the item is in stock.");

// https://schema.org/InStoreOnly
t("InStoreOnly");
t("Indicates that the item is available only at physical locations.");

// https://schema.org/InsuranceAgency
t("InsuranceAgency");
t("An Insurance agency.");

// https://schema.org/Intangible
t("Intangible");
t("A utility class that serves as the umbrella for a number of 'intangible' things such as quantities, structured values, etc.");

// https://schema.org/Integer
t("Integer");
t("Data type: Integer.");

// https://schema.org/InteractAction
t("InteractAction");
t("The act of interacting with another person or organization.");

// https://schema.org/InteractionCounter
t("InteractionCounter");
t("A summary of how users have interacted with this CreativeWork. In most cases, authors will use a subtype to specify the specific type of interaction.");
t("A summary of how users have interacted with this CreativeWork.");

// https://schema.org/InternationalTrial
t("InternationalTrial");
t("An international trial.");

// https://schema.org/InternetCafe
t("InternetCafe");
t("An internet cafe.");

// https://schema.org/InvestmentFund
t("InvestmentFund");
t("A company or fund that gathers capital from a number of investors to create a pool of money that is then re-invested into stocks, bonds and other assets.");

// https://schema.org/InvestmentOrDeposit
t("InvestmentOrDeposit");
t("A type of financial product that typically requires the client to transfer funds to a financial service in return for potential beneficial financial return.");

// https://schema.org/InviteAction
t("InviteAction");
t("The act of asking someone to attend an event. Reciprocal of RsvpAction.");
t("The act of asking someone to attend an event.");

// https://schema.org/Invoice
t("Invoice");
t("A statement of the money due for goods or services; a bill.");

// https://schema.org/InvoicePrice
t("InvoicePrice");
t("Represents the invoice price of an offered product.");

// https://schema.org/IOSPlatform
t("IOSPlatform");
t("Represents the broad notion of iOS-based operating systems.");

// https://schema.org/IPTCDigitalSourceEnumeration
t("IPTCDigitalSourceEnumeration");
t('<a href="https://www.iptc.org/">IPTC</a> "Digital Source" codes for use with the <a class="localLink" href="/digitalSourceType">digitalSourceType</a> property, providing information about the source for a digital media object.
In general these codes are not declared here to be mutually exclusive, although some combinations would be contradictory if applied simultaneously, or might be considered mutually incompatible by upstream maintainers of the definitions. See the IPTC <a href="https://www.iptc.org/std/photometadata/documentation/userguide/">documentation</a>
 for <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">detailed definitions</a> of all terms.');
t('<a href="https://www.iptc.org/">IPTC</a> "Digital Source" codes for use with the <a class="localLink" href="/digitalSourceType">digitalSourceType</a> property, providing information about the source for a digital media object.');

// https://schema.org/ItemAvailability
t("ItemAvailability");
t("A list of possible product availability options.");

// https://schema.org/ItemList
t("ItemList");
t("A list of items of any sort&#x2014;for example, Top 10 Movies About Weathermen, or Top 100 Party Songs. Not to be confused with HTML lists, which are often used only for formatting.");
t("A list of items of any sort&#x2014;for example, Top 10 Movies About Weathermen, or Top 100 Party Songs.");

// https://schema.org/ItemListOrderAscending
t("ItemListOrderAscending");
t("An ItemList ordered with lower values listed first.");

// https://schema.org/ItemListOrderDescending
t("ItemListOrderDescending");
t("An ItemList ordered with higher values listed first.");

// https://schema.org/ItemListOrderType
t("ItemListOrderType");
t("Enumerated for values for itemListOrder for indicating how an ordered ItemList is organized.");

// https://schema.org/ItemListUnordered
t("ItemListUnordered");
t("An ItemList ordered with no explicit order.");

// https://schema.org/ItemPage
t("ItemPage");
t("A page devoted to a single item, such as a particular product or hotel.");

// https://schema.org/JewelryStore
t("JewelryStore");
t("A jewelry store.");

// https://schema.org/JobPosting
t("JobPosting");
t("A listing that describes a job opening in a certain organization.");

// https://schema.org/JoinAction
t("JoinAction");
t('An agent joins an event/group with participants/friends at a location.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/RegisterAction">RegisterAction</a>: Unlike RegisterAction, JoinAction refers to joining a group/team of people.</li>
<li><a class="localLink" href="/SubscribeAction">SubscribeAction</a>: Unlike SubscribeAction, JoinAction does not imply that you\'ll be receiving updates.</li>
<li><a class="localLink" href="/FollowAction">FollowAction</a>: Unlike FollowAction, JoinAction does not imply that you\'ll be polling for updates.</li>
</ul>');
t("An agent joins an event/group with participants/friends at a location.");

// https://schema.org/Joint
t("Joint");
t("The anatomical location at which two or more bones make contact.");

// https://schema.org/KeepProduct
t("KeepProduct");
t("Specifies that the consumer can keep the product, even when receiving a refund or store credit.");

// https://schema.org/KosherDiet
t("KosherDiet");
t("A diet conforming to Jewish dietary practices.");

// https://schema.org/LaboratoryScience
t("LaboratoryScience");
t("A medical science pertaining to chemical, hematological, immunologic, microscopic, or bacteriological diagnostic analyses or research.");

// https://schema.org/LakeBodyOfWater
t("LakeBodyOfWater");
t("A lake (for example, Lake Pontrachain).");

// https://schema.org/Landform
t("Landform");
t("A landform or physical feature.  Landform elements include mountains, plains, lakes, rivers, seascape and oceanic waterbody interface features such as bays, peninsulas, seas and so forth, including sub-aqueous terrain features such as submersed mountain ranges, volcanoes, and the great ocean basins.");
t("A landform or physical feature.");

// https://schema.org/LandmarksOrHistoricalBuildings
t("LandmarksOrHistoricalBuildings");
t("An historical landmark or building.");

// https://schema.org/Language
t("Language");
t('Natural languages such as Spanish, Tamil, Hindi, English, etc. Formal language code tags expressed in <a href="https://en.wikipedia.org/wiki/IETF_language_tag">BCP 47</a> can be used via the <a class="localLink" href="/alternateName">alternateName</a> property. The Language type previously also covered programming languages such as Scheme and Lisp, which are now best represented using <a class="localLink" href="/ComputerLanguage">ComputerLanguage</a>.');
t('Natural languages such as Spanish, Tamil, Hindi, English, etc. Formal language code tags expressed in <a href="https://en.wikipedia.org/wiki/IETF_language_tag">BCP 47</a> can be used via the <a class="localLink" href="/alternateName">alternateName</a> property.');

// https://schema.org/LaserDiscFormat
t("LaserDiscFormat");
t("LaserDiscFormat.");

// https://schema.org/LearningResource
t("LearningResource");
t('The LearningResource type can be used to indicate <a class="localLink" href="/CreativeWork">CreativeWork</a>s (whether physical or digital) that have a particular and explicit orientation towards learning, education, skill acquisition, and other educational purposes.<br/><br/>

<a class="localLink" href="/LearningResource">LearningResource</a> is expected to be used as an addition to a primary type such as <a class="localLink" href="/Book">Book</a>, <a class="localLink" href="/VideoObject">VideoObject</a>, <a class="localLink" href="/Product">Product</a> etc.<br/><br/>

<a class="localLink" href="/EducationEvent">EducationEvent</a> serves a similar purpose for event-like things (e.g. a <a class="localLink" href="/Trip">Trip</a>). A <a class="localLink" href="/LearningResource">LearningResource</a> may be created as a result of an <a class="localLink" href="/EducationEvent">EducationEvent</a>, for example by recording one.');
t('The LearningResource type can be used to indicate <a class="localLink" href="/CreativeWork">CreativeWork</a>s (whether physical or digital) that have a particular and explicit orientation towards learning, education, skill acquisition, and other educational purposes.');

// https://schema.org/LeaveAction
t("LeaveAction");
t('An agent leaves an event / group with participants/friends at a location.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/JoinAction">JoinAction</a>: The antonym of LeaveAction.</li>
<li><a class="localLink" href="/UnRegisterAction">UnRegisterAction</a>: Unlike UnRegisterAction, LeaveAction implies leaving a group/team of people rather than a service.</li>
</ul>');
t("An agent leaves an event / group with participants/friends at a location.");

// https://schema.org/LeftHandDriving
t("LeftHandDriving");
t("The steering position is on the left side of the vehicle (viewed from the main direction of driving).");

// https://schema.org/LegalForceStatus
t("LegalForceStatus");
t("A list of possible statuses for the legal force of a legislation.");

// https://schema.org/LegalService
t("LegalService");
t('A LegalService is a business that provides legally-oriented services, advice and representation, e.g. law firms.<br/><br/>

As a <a class="localLink" href="/LocalBusiness">LocalBusiness</a> it can be described as a <a class="localLink" href="/provider">provider</a> of one or more <a class="localLink" href="/Service">Service</a>(s).');
t("A LegalService is a business that provides legally-oriented services, advice and representation, e.g. law firms.");

// https://schema.org/LegalValueLevel
t("LegalValueLevel");
t("A list of possible levels for the legal validity of a legislation.");

// https://schema.org/Legislation
t("Legislation");
t("A legal document such as an act, decree, bill, etc. (enforceable or not) or a component of a legal act (like an article).");

// https://schema.org/LegislationObject
t("LegislationObject");
t("A specific object or file containing a Legislation. Note that the same Legislation can be published in multiple files. For example, a digitally signed PDF, a plain PDF and an HTML version.");
t("A specific object or file containing a Legislation.");

// https://schema.org/LegislativeBuilding
t("LegislativeBuilding");
t("A legislative building&#x2014;for example, the state capitol.");

// https://schema.org/LeisureTimeActivity
t("LeisureTimeActivity");
t("Any physical activity engaged in for recreational purposes. Examples may include ballroom dancing, roller skating, canoeing, fishing, etc.");
t("Any physical activity engaged in for recreational purposes.");

// https://schema.org/LendAction
t("LendAction");
t('The act of providing an object under an agreement that it will be returned at a later date. Reciprocal of BorrowAction.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/BorrowAction">BorrowAction</a>: Reciprocal of LendAction.</li>
</ul>');
t("The act of providing an object under an agreement that it will be returned at a later date.");

// https://schema.org/Library
t("Library");
t("A library.");

// https://schema.org/LibrarySystem
t("LibrarySystem");
t('A <a class="localLink" href="/LibrarySystem">LibrarySystem</a> is a collaborative system amongst several libraries.');

// https://schema.org/LifestyleModification
t("LifestyleModification");
t("A process of care involving exercise, changes to diet, fitness routines, and other lifestyle changes aimed at improving a health condition.");

// https://schema.org/Ligament
t("Ligament");
t("A short band of tough, flexible, fibrous connective tissue that functions to connect multiple bones, cartilages, and structurally support joints.");

// https://schema.org/LikeAction
t("LikeAction");
t("The act of expressing a positive sentiment about the object. An agent likes an object (a proposition, topic or theme) with participants.");
t("The act of expressing a positive sentiment about the object.");

// https://schema.org/LimitedAvailability
t("LimitedAvailability");
t("Indicates that the item has limited availability.");

// https://schema.org/LimitedByGuaranteeCharity
t("LimitedByGuaranteeCharity");
t("LimitedByGuaranteeCharity: Non-profit type referring to a charitable company that is limited by guarantee (UK).");

// https://schema.org/LinkRole
t("LinkRole");
t("A Role that represents a Web link, e.g. as expressed via the 'url' property. Its linkRelationship property can indicate URL-based and plain textual link types, e.g. those in IANA link registry or others such as 'amphtml'. This structure provides a placeholder where details from HTML's link element can be represented outside of HTML, e.g. in JSON-LD feeds.");
t("A Role that represents a Web link, e.g. as expressed via the 'url' property.");

// https://schema.org/LiquorStore
t("LiquorStore");
t("A shop that sells alcoholic drinks such as wine, beer, whisky and other spirits.");

// https://schema.org/ListenAction
t("ListenAction");
t("The act of consuming audio content.");

// https://schema.org/ListItem
t("ListItem");
t("An list item, e.g. a step in a checklist or how-to description.");

// https://schema.org/ListPrice
t("ListPrice");
t("Represents the list price (the price a product is actually advertised for) of an offered product.");

// https://schema.org/LiteraryEvent
t("LiteraryEvent");
t("Event type: Literary event.");

// https://schema.org/LiveAlbum
t("LiveAlbum");
t("LiveAlbum.");

// https://schema.org/LiveBlogPosting
t("LiveBlogPosting");
t('A <a class="localLink" href="/LiveBlogPosting">LiveBlogPosting</a> is a <a class="localLink" href="/BlogPosting">BlogPosting</a> intended to provide a rolling textual coverage of an ongoing event through continuous updates.');

// https://schema.org/LivingWithHealthAspect
t("LivingWithHealthAspect");
t("Information about coping or life related to the topic.");

// https://schema.org/LoanOrCredit
t("LoanOrCredit");
t("A financial product for the loaning of an amount of money, or line of credit, under agreed terms and charges.");

// https://schema.org/LocalBusiness
t("LocalBusiness");
t("A particular physical business or branch of an organization. Examples of LocalBusiness include a restaurant, a particular branch of a restaurant chain, a branch of a bank, a medical practice, a club, a bowling alley, etc.");
t("A particular physical business or branch of an organization.");

// https://schema.org/LocationFeatureSpecification
t("LocationFeatureSpecification");
t("Specifies a location feature by providing a structured value representing a feature of an accommodation as a property-value pair of varying degrees of formality.");

// https://schema.org/LockerDelivery
t("LockerDelivery");
t("A DeliveryMethod in which an item is made available via locker.");

// https://schema.org/Locksmith
t("Locksmith");
t("A locksmith.");

// https://schema.org/LodgingBusiness
t("LodgingBusiness");
t("A lodging business, such as a motel, hotel, or inn.");

// https://schema.org/LodgingReservation
t("LodgingReservation");
t("A reservation for lodging at a hotel, motel, inn, etc.<br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.");

// https://schema.org/Longitudinal
t("Longitudinal");
t("Unlike cross-sectional studies, longitudinal studies track the same people, and therefore the differences observed in those people are less likely to be the result of cultural differences across generations. Longitudinal studies are also used in medicine to uncover predictors of certain diseases.");
t("Unlike cross-sectional studies, longitudinal studies track the same people, and therefore the differences observed in those people are less likely to be the result of cultural differences across generations.");

// https://schema.org/LoseAction
t("LoseAction");
t("The act of being defeated in a competitive activity.");

// https://schema.org/LowCalorieDiet
t("LowCalorieDiet");
t("A diet focused on reduced calorie intake.");

// https://schema.org/LowFatDiet
t("LowFatDiet");
t("A diet focused on reduced fat and cholesterol intake.");

// https://schema.org/LowLactoseDiet
t("LowLactoseDiet");
t("A diet appropriate for people with lactose intolerance.");

// https://schema.org/LowSaltDiet
t("LowSaltDiet");
t("A diet focused on reduced sodium intake.");

// https://schema.org/Lung
t("Lung");
t("Lung and respiratory system clinical examination.");

// https://schema.org/LymphaticVessel
t("LymphaticVessel");
t("A type of blood vessel that specifically carries lymph fluid unidirectionally toward the heart.");

// https://schema.org/Male
t("Male");
t("The male gender.");

// https://schema.org/Manuscript
t("Manuscript");
t("A book, document, or piece of music written by hand rather than typed or printed.");

// https://schema.org/Map
t("Map");
t("A map.");

// https://schema.org/MapCategoryType
t("MapCategoryType");
t("An enumeration of several kinds of Map.");

// https://schema.org/MarryAction
t("MarryAction");
t("The act of marrying a person.");

// https://schema.org/Mass
t("Mass");
t("Properties that take Mass as values are of the form '&lt;Number&gt; &lt;Mass unit of measure&gt;'. E.g., '7 kg'.");
t("Properties that take Mass as values are of the form '&lt;Number&gt; &lt;Mass unit of measure&gt;'.");

// https://schema.org/MathSolver
t("MathSolver");
t("A math solver which is capable of solving a subset of mathematical problems.");

// https://schema.org/MaximumDoseSchedule
t("MaximumDoseSchedule");
t("The maximum dosing schedule considered safe for a drug or supplement as recommended by an authority or by the drug/supplement's manufacturer. Capture the recommending authority in the recognizingAuthority property of MedicalEntity.");
t("The maximum dosing schedule considered safe for a drug or supplement as recommended by an authority or by the drug/supplement's manufacturer.");

// https://schema.org/MayTreatHealthAspect
t("MayTreatHealthAspect");
t("Related topics may be treated by a Topic.");

// https://schema.org/MeasurementMethodEnum
t("MeasurementMethodEnum");
t('Enumeration(s) for use with <a class="localLink" href="/measurementMethod">measurementMethod</a>.');

// https://schema.org/MeasurementTypeEnumeration
t("MeasurementTypeEnumeration");
t('Enumeration of common measurement types (or dimensions), for example "chest" for a person, "inseam" for pants, "gauge" for screws, or "wheel" for bicycles.');

// https://schema.org/MediaEnumeration
t("MediaEnumeration");
t("MediaEnumeration enumerations are lists of codes, labels etc. useful for describing media objects. They may be reflections of externally developed lists, or created at schema.org, or a combination.");
t("MediaEnumeration enumerations are lists of codes, labels etc. useful for describing media objects.");

// https://schema.org/MediaGallery
t("MediaGallery");
t("Web page type: Media gallery page. A mixed-media page that can contain media such as images, videos, and other multimedia.");
t("Web page type: Media gallery page.");

// https://schema.org/MediaManipulationRatingEnumeration
t("MediaManipulationRatingEnumeration");
t('Codes for use with the <a class="localLink" href="/mediaAuthenticityCategory">mediaAuthenticityCategory</a> property, indicating the authenticity of a media object (in the context of how it was published or shared). In general these codes are not mutually exclusive, although some combinations (such as \'original\' versus \'transformed\', \'edited\' and \'staged\') would be contradictory if applied in the same <a class="localLink" href="/MediaReview">MediaReview</a>. Note that the application of these codes is with regard to a piece of media shared or published in a particular context.');
t('Codes for use with the <a class="localLink" href="/mediaAuthenticityCategory">mediaAuthenticityCategory</a> property, indicating the authenticity of a media object (in the context of how it was published or shared).');

// https://schema.org/MediaObject
t("MediaObject");
t("A media object, such as an image, video, audio, or text object embedded in a web page or a downloadable dataset i.e. DataDownload. Note that a creative work may have many media objects associated with it on the same web page. For example, a page about a single song (MusicRecording) may have a music video (VideoObject), and a high and low bandwidth audio stream (2 AudioObject's).");
t("A media object, such as an image, video, audio, or text object embedded in a web page or a downloadable dataset i.e. DataDownload.");

// https://schema.org/MediaReview
t("MediaReview");
t('A <a class="localLink" href="/MediaReview">MediaReview</a> is a more specialized form of Review dedicated to the evaluation of media content online, typically in the context of fact-checking and misinformation.
    For more general reviews of media in the broader sense, use <a class="localLink" href="/UserReview">UserReview</a>, <a class="localLink" href="/CriticReview">CriticReview</a> or other <a class="localLink" href="/Review">Review</a> types. This definition is
    a work in progress. While the <a class="localLink" href="/MediaManipulationRatingEnumeration">MediaManipulationRatingEnumeration</a> list reflects significant community review amongst fact-checkers and others working
    to combat misinformation, the specific structures for representing media objects, their versions and publication context, are still evolving. Similarly, best practices for the relationship between <a class="localLink" href="/MediaReview">MediaReview</a> and <a class="localLink" href="/ClaimReview">ClaimReview</a> markup have not yet been finalized.');
t('A <a class="localLink" href="/MediaReview">MediaReview</a> is a more specialized form of Review dedicated to the evaluation of media content online, typically in the context of fact-checking and misinformation.');

// https://schema.org/MediaReviewItem
t("MediaReviewItem");
t('Represents an item or group of closely related items treated as a unit for the sake of evaluation in a <a class="localLink" href="/MediaReview">MediaReview</a>. Authorship etc. apply to the items rather than to the curation/grouping or reviewing party.');
t('Represents an item or group of closely related items treated as a unit for the sake of evaluation in a <a class="localLink" href="/MediaReview">MediaReview</a>.');

// https://schema.org/MediaSubscription
t("MediaSubscription");
t("A subscription which allows a user to access media including audio, video, books, etc.");

// https://schema.org/MedicalAudience
t("MedicalAudience");
t("Target audiences for medical web pages.");

// https://schema.org/MedicalAudienceType
t("MedicalAudienceType");
t("Target audiences types for medical web pages. Enumerated type.");
t("Target audiences types for medical web pages.");

// https://schema.org/MedicalBusiness
t("MedicalBusiness");
t("A particular physical or virtual business of an organization for medical purposes. Examples of MedicalBusiness include different businesses run by health professionals.");
t("A particular physical or virtual business of an organization for medical purposes.");

// https://schema.org/MedicalCause
t("MedicalCause");
t("The causative agent(s) that are responsible for the pathophysiologic process that eventually results in a medical condition, symptom or sign. In this schema, unless otherwise specified this is meant to be the proximate cause of the medical condition, symptom or sign. The proximate cause is defined as the causative agent that most directly results in the medical condition, symptom or sign. For example, the HIV virus could be considered a cause of AIDS. Or in a diagnostic context, if a patient fell and sustained a hip fracture and two days later sustained a pulmonary embolism which eventuated in a cardiac arrest, the cause of the cardiac arrest (the proximate cause) would be the pulmonary embolism and not the fall. Medical causes can include cardiovascular, chemical, dermatologic, endocrine, environmental, gastroenterologic, genetic, hematologic, gynecologic, iatrogenic, infectious, musculoskeletal, neurologic, nutritional, obstetric, oncologic, otolaryngologic, pharmacologic, psychiatric, pulmonary, renal, rheumatologic, toxic, traumatic, or urologic causes; medical conditions can be causes as well.");
t("The causative agent(s) that are responsible for the pathophysiologic process that eventually results in a medical condition, symptom or sign.");

// https://schema.org/MedicalClinic
t("MedicalClinic");
t("A facility, often associated with a hospital or medical school, that is devoted to the specific diagnosis and/or healthcare. Previously limited to outpatients but with evolution it may be open to inpatients as well.");
t("A facility, often associated with a hospital or medical school, that is devoted to the specific diagnosis and/or healthcare.");

// https://schema.org/MedicalCode
t("MedicalCode");
t("A code for a medical entity.");

// https://schema.org/MedicalCondition
t("MedicalCondition");
t("Any condition of the human body that affects the normal functioning of a person, whether physically or mentally. Includes diseases, injuries, disabilities, disorders, syndromes, etc.");
t("Any condition of the human body that affects the normal functioning of a person, whether physically or mentally.");

// https://schema.org/MedicalConditionStage
t("MedicalConditionStage");
t("A stage of a medical condition, such as 'Stage IIIa'.");

// https://schema.org/MedicalContraindication
t("MedicalContraindication");
t("A condition or factor that serves as a reason to withhold a certain medical therapy. Contraindications can be absolute (there are no reasonable circumstances for undertaking a course of action) or relative (the patient is at higher risk of complications, but these risks may be outweighed by other considerations or mitigated by other measures).");
t("A condition or factor that serves as a reason to withhold a certain medical therapy.");

// https://schema.org/MedicalDevice
t("MedicalDevice");
t("Any object used in a medical capacity, such as to diagnose or treat a patient.");

// https://schema.org/MedicalDevicePurpose
t("MedicalDevicePurpose");
t("Categories of medical devices, organized by the purpose or intended use of the device.");

// https://schema.org/MedicalEntity
t("MedicalEntity");
t("The most generic type of entity related to health and the practice of medicine.");

// https://schema.org/MedicalEnumeration
t("MedicalEnumeration");
t("Enumerations related to health and the practice of medicine: A concept that is used to attribute a quality to another concept, as a qualifier, a collection of items or a listing of all of the elements of a set in medicine practice.");

// https://schema.org/MedicalEvidenceLevel
t("MedicalEvidenceLevel");
t("Level of evidence for a medical guideline. Enumerated type.");
t("Level of evidence for a medical guideline.");

// https://schema.org/MedicalGuideline
t("MedicalGuideline");
t("Any recommendation made by a standard society (e.g. ACC/AHA) or consensus statement that denotes how to diagnose and treat a particular condition. Note: this type should be used to tag the actual guideline recommendation; if the guideline recommendation occurs in a larger scholarly article, use MedicalScholarlyArticle to tag the overall article, not this type. Note also: the organization making the recommendation should be captured in the recognizingAuthority base property of MedicalEntity.");
t("Any recommendation made by a standard society (e.g. ACC/AHA) or consensus statement that denotes how to diagnose and treat a particular condition.");

// https://schema.org/MedicalGuidelineContraindication
t("MedicalGuidelineContraindication");
t("A guideline contraindication that designates a process as harmful and where quality of the data supporting the contraindication is sound.");

// https://schema.org/MedicalGuidelineRecommendation
t("MedicalGuidelineRecommendation");
t("A guideline recommendation that is regarded as efficacious and where quality of the data supporting the recommendation is sound.");

// https://schema.org/MedicalImagingTechnique
t("MedicalImagingTechnique");
t("Any medical imaging modality typically used for diagnostic purposes. Enumerated type.");
t("Any medical imaging modality typically used for diagnostic purposes.");

// https://schema.org/MedicalIndication
t("MedicalIndication");
t("A condition or factor that indicates use of a medical therapy, including signs, symptoms, risk factors, anatomical states, etc.");

// https://schema.org/MedicalIntangible
t("MedicalIntangible");
t("A utility class that serves as the umbrella for a number of 'intangible' things in the medical space.");

// https://schema.org/MedicalObservationalStudy
t("MedicalObservationalStudy");
t("An observational study is a type of medical study that attempts to infer the possible effect of a treatment through observation of a cohort of subjects over a period of time. In an observational study, the assignment of subjects into treatment groups versus control groups is outside the control of the investigator. This is in contrast with controlled studies, such as the randomized controlled trials represented by MedicalTrial, where each subject is randomly assigned to a treatment group or a control group before the start of the treatment.");
t("An observational study is a type of medical study that attempts to infer the possible effect of a treatment through observation of a cohort of subjects over a period of time.");

// https://schema.org/MedicalObservationalStudyDesign
t("MedicalObservationalStudyDesign");
t("Design models for observational medical studies. Enumerated type.");
t("Design models for observational medical studies.");

// https://schema.org/MedicalOrganization
t("MedicalOrganization");
t("A medical organization (physical or not), such as hospital, institution or clinic.");

// https://schema.org/MedicalProcedure
t("MedicalProcedure");
t("A process of care used in either a diagnostic, therapeutic, preventive or palliative capacity that relies on invasive (surgical), non-invasive, or other techniques.");

// https://schema.org/MedicalProcedureType
t("MedicalProcedureType");
t("An enumeration that describes different types of medical procedures.");

// https://schema.org/MedicalResearcher
t("MedicalResearcher");
t("Medical researchers.");

// https://schema.org/MedicalRiskCalculator
t("MedicalRiskCalculator");
t("A complex mathematical calculation requiring an online calculator, used to assess prognosis. Note: use the url property of Thing to record any URLs for online calculators.");
t("A complex mathematical calculation requiring an online calculator, used to assess prognosis.");

// https://schema.org/MedicalRiskEstimator
t("MedicalRiskEstimator");
t("Any rule set or interactive tool for estimating the risk of developing a complication or condition.");

// https://schema.org/MedicalRiskFactor
t("MedicalRiskFactor");
t("A risk factor is anything that increases a person's likelihood of developing or contracting a disease, medical condition, or complication.");

// https://schema.org/MedicalRiskScore
t("MedicalRiskScore");
t("A simple system that adds up the number of risk factors to yield a score that is associated with prognosis, e.g. CHAD score, TIMI risk score.");

// https://schema.org/MedicalScholarlyArticle
t("MedicalScholarlyArticle");
t("A scholarly article in the medical domain.");

// https://schema.org/MedicalSign
t("MedicalSign");
t("Any physical manifestation of a person's medical condition discoverable by objective diagnostic tests or physical examination.");

// https://schema.org/MedicalSignOrSymptom
t("MedicalSignOrSymptom");
t("Any feature associated or not with a medical condition. In medicine a symptom is generally subjective while a sign is objective.");
t("Any feature associated or not with a medical condition.");

// https://schema.org/MedicalSpecialty
t("MedicalSpecialty");
t("Any specific branch of medical science or practice. Medical specialities include clinical specialties that pertain to particular organ systems and their respective disease states, as well as allied health specialties. Enumerated type.");
t("Any specific branch of medical science or practice.");

// https://schema.org/MedicalStudy
t("MedicalStudy");
t("A medical study is an umbrella type covering all kinds of research studies relating to human medicine or health, including observational studies and interventional trials and registries, randomized, controlled or not. When the specific type of study is known, use one of the extensions of this type, such as MedicalTrial or MedicalObservationalStudy. Also, note that this type should be used to mark up data that describes the study itself; to tag an article that publishes the results of a study, use MedicalScholarlyArticle. Note: use the code property of MedicalEntity to store study IDs, e.g. clinicaltrials.gov ID.");
t("A medical study is an umbrella type covering all kinds of research studies relating to human medicine or health, including observational studies and interventional trials and registries, randomized, controlled or not.");

// https://schema.org/MedicalStudyStatus
t("MedicalStudyStatus");
t("The status of a medical study. Enumerated type.");
t("The status of a medical study.");

// https://schema.org/MedicalSymptom
t("MedicalSymptom");
t("Any complaint sensed and expressed by the patient (therefore defined as subjective)  like stomachache, lower-back pain, or fatigue.");

// https://schema.org/MedicalTest
t("MedicalTest");
t("Any medical test, typically performed for diagnostic purposes.");

// https://schema.org/MedicalTestPanel
t("MedicalTestPanel");
t("Any collection of tests commonly ordered together.");

// https://schema.org/MedicalTherapy
t("MedicalTherapy");
t("Any medical intervention designed to prevent, treat, and cure human diseases and medical conditions, including both curative and palliative therapies. Medical therapies are typically processes of care relying upon pharmacotherapy, behavioral therapy, supportive therapy (with fluid or nutrition for example), or detoxification (e.g. hemodialysis) aimed at improving or preventing a health condition.");
t("Any medical intervention designed to prevent, treat, and cure human diseases and medical conditions, including both curative and palliative therapies.");

// https://schema.org/MedicalTrial
t("MedicalTrial");
t("A medical trial is a type of medical study that uses a scientific process to compare the safety and efficacy of medical therapies or medical procedures. In general, medical trials are controlled and subjects are allocated at random to the different treatment and/or control groups.");
t("A medical trial is a type of medical study that uses a scientific process to compare the safety and efficacy of medical therapies or medical procedures.");

// https://schema.org/MedicalTrialDesign
t("MedicalTrialDesign");
t("Design models for medical trials. Enumerated type.");
t("Design models for medical trials.");

// https://schema.org/MedicalWebPage
t("MedicalWebPage");
t("A web page that provides medical information.");

// https://schema.org/MedicineSystem
t("MedicineSystem");
t("Systems of medical practice.");

// https://schema.org/MeetingRoom
t("MeetingRoom");
t('A meeting room, conference room, or conference hall is a room provided for singular events such as business conferences and meetings (source: Wikipedia, the free encyclopedia, see <a href="http://en.wikipedia.org/wiki/Conference_hall">http://en.wikipedia.org/wiki/Conference_hall</a>).
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("A meeting room, conference room, or conference hall is a room provided for singular events such as business conferences and meetings.");

// https://schema.org/MensClothingStore
t("MensClothingStore");
t("A men's clothing store.");

// https://schema.org/Menu
t("Menu");
t("A structured representation of food or drink items available from a FoodEstablishment.");

// https://schema.org/MenuItem
t("MenuItem");
t("A food or drink item listed in a menu or menu section.");

// https://schema.org/MenuSection
t("MenuSection");
t("A sub-grouping of food or drink items in a menu. E.g. courses (such as 'Dinner', 'Breakfast', etc.), specific type of dishes (such as 'Meat', 'Vegan', 'Drinks', etc.), or some other classification made by the menu provider.");
t("A sub-grouping of food or drink items in a menu.");

// https://schema.org/MerchantReturnEnumeration
t("MerchantReturnEnumeration");
t("Enumerates several kinds of product return policies.");

// https://schema.org/MerchantReturnFiniteReturnWindow
t("MerchantReturnFiniteReturnWindow");
t("Specifies that there is a finite window for product returns.");

// https://schema.org/MerchantReturnNotPermitted
t("MerchantReturnNotPermitted");
t("Specifies that product returns are not permitted.");

// https://schema.org/MerchantReturnPolicy
t("MerchantReturnPolicy");
t('A MerchantReturnPolicy provides information about product return policies associated with an <a class="localLink" href="/Organization">Organization</a>, <a class="localLink" href="/Product">Product</a>, or <a class="localLink" href="/Offer">Offer</a>.');

// https://schema.org/MerchantReturnPolicySeasonalOverride
t("MerchantReturnPolicySeasonalOverride");
t("A seasonal override of a return policy, for example used for holidays.");

// https://schema.org/MerchantReturnUnlimitedWindow
t("MerchantReturnUnlimitedWindow");
t("Specifies that there is an unlimited window for product returns.");

// https://schema.org/MerchantReturnUnspecified
t("MerchantReturnUnspecified");
t("Specifies that a product return policy is not provided.");

// https://schema.org/Message
t("Message");
t("A single message from a sender to one or more organizations or people.");

// https://schema.org/MiddleSchool
t("MiddleSchool");
t("A middle school (typically for children aged around 11-14, although this varies somewhat).");

// https://schema.org/Midwifery
t("Midwifery");
t("A nurse-like health profession that deals with pregnancy, childbirth, and the postpartum period (including care of the newborn), besides sexual and reproductive health of women throughout their lives.");

// https://schema.org/MinimumAdvertisedPrice
t("MinimumAdvertisedPrice");
t('Represents the minimum advertised price ("MAP") (as dictated by the manufacturer) of an offered product.');

// https://schema.org/MinorHumanEditsDigitalSource
t("MinorHumanEditsDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/minorHumanEdits">minor human edits</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/MisconceptionsHealthAspect
t("MisconceptionsHealthAspect");
t("Content about common misconceptions and myths that are related to a topic.");

// https://schema.org/MixedEventAttendanceMode
t("MixedEventAttendanceMode");
t("MixedEventAttendanceMode - an event that is conducted as a combination of both offline and online modes.");

// https://schema.org/MixtapeAlbum
t("MixtapeAlbum");
t("MixtapeAlbum.");

// https://schema.org/MobileApplication
t("MobileApplication");
t("A software application designed specifically to work well on a mobile device such as a telephone.");

// https://schema.org/MobilePhoneStore
t("MobilePhoneStore");
t("A store that sells mobile phones and related accessories.");

// https://schema.org/MobileWebPlatform
t("MobileWebPlatform");
t("Represents the broad notion of 'mobile' browsers as a Web Platform.");

// https://schema.org/MolecularEntity
t("MolecularEntity");
t("Any constitutionally or isotopically distinct atom, molecule, ion, ion pair, radical, radical ion, complex, conformer etc., identifiable as a separately distinguishable entity.");

// https://schema.org/Monday
t("Monday");
t("The day of the week between Sunday and Tuesday.");

// https://schema.org/MonetaryAmount
t("MonetaryAmount");
t('A monetary value or range. This type can be used to describe an amount of money such as $50 USD, or a range as in describing a bank account being suitable for a balance between £1,000 and £1,000,000 GBP, or the value of a salary, etc. It is recommended to use <a class="localLink" href="/PriceSpecification">PriceSpecification</a> Types to describe the price of an Offer, Invoice, etc.');
t("A monetary value or range.");

// https://schema.org/MonetaryAmountDistribution
t("MonetaryAmountDistribution");
t("A statistical distribution of monetary amounts.");

// https://schema.org/MonetaryGrant
t("MonetaryGrant");
t("A monetary grant.");

// https://schema.org/MoneyTransfer
t("MoneyTransfer");
t("The act of transferring money from one place to another place. This may occur electronically or physically.");
t("The act of transferring money from one place to another place.");

// https://schema.org/MortgageLoan
t("MortgageLoan");
t("A loan in which property or real estate is used as collateral. (A loan securitized against some real estate.)");
t("A loan in which property or real estate is used as collateral.");

// https://schema.org/Mosque
t("Mosque");
t("A mosque.");

// https://schema.org/Motel
t("Motel");
t('A motel.
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("A motel.");

// https://schema.org/Motorcycle
t("Motorcycle");
t("A motorcycle or motorbike is a single-track, two-wheeled motor vehicle.");

// https://schema.org/MotorcycleDealer
t("MotorcycleDealer");
t("A motorcycle dealer.");

// https://schema.org/MotorcycleRepair
t("MotorcycleRepair");
t("A motorcycle repair shop.");

// https://schema.org/MotorizedBicycle
t("MotorizedBicycle");
t("A motorized bicycle is a bicycle with an attached motor used to power the vehicle, or to assist with pedaling.");

// https://schema.org/Mountain
t("Mountain");
t("A mountain, like Mount Whitney or Mount Everest.");

// https://schema.org/MoveAction
t("MoveAction");
t('The act of an agent relocating to a place.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/TransferAction">TransferAction</a>: Unlike TransferAction, the subject of the move is a living Person or Organization rather than an inanimate object.</li>
</ul>');
t("The act of an agent relocating to a place.");

// https://schema.org/Movie
t("Movie");
t("A movie.");

// https://schema.org/MovieClip
t("MovieClip");
t("A short segment/part of a movie.");

// https://schema.org/MovieRentalStore
t("MovieRentalStore");
t("A movie rental store.");

// https://schema.org/MovieSeries
t("MovieSeries");
t("A series of movies. Included movies can be indicated with the hasPart property.");
t("A series of movies.");

// https://schema.org/MovieTheater
t("MovieTheater");
t("A movie theater.");

// https://schema.org/MovingCompany
t("MovingCompany");
t("A moving company.");

// https://schema.org/MRI
t("MRI");
t("Magnetic resonance imaging.");

// https://schema.org/MSRP
t("MSRP");
t('Represents the manufacturer suggested retail price ("MSRP") of an offered product.');

// https://schema.org/MulticellularParasite
t("MulticellularParasite");
t("Multicellular parasite that causes an infection.");

// https://schema.org/MultiCenterTrial
t("MultiCenterTrial");
t("A trial that takes place at multiple centers.");

// https://schema.org/MultiPlayer
t("MultiPlayer");
t("Play mode: MultiPlayer. Requiring or allowing multiple human players to play simultaneously.");
t("Play mode: MultiPlayer.");

// https://schema.org/Muscle
t("Muscle");
t("A muscle is an anatomical structure consisting of a contractile form of tissue that animals use to effect movement.");

// https://schema.org/Musculoskeletal
t("Musculoskeletal");
t("A specific branch of medical science that pertains to diagnosis and treatment of disorders of muscles, ligaments and skeletal system.");

// https://schema.org/MusculoskeletalExam
t("MusculoskeletalExam");
t("Musculoskeletal system clinical examination.");

// https://schema.org/Museum
t("Museum");
t("A museum.");

// https://schema.org/MusicAlbum
t("MusicAlbum");
t("A collection of music tracks.");

// https://schema.org/MusicAlbumProductionType
t("MusicAlbumProductionType");
t("Classification of the album by its type of content: soundtrack, live album, studio album, etc.");

// https://schema.org/MusicAlbumReleaseType
t("MusicAlbumReleaseType");
t("The kind of release which this album is: single, EP or album.");

// https://schema.org/MusicComposition
t("MusicComposition");
t("A musical composition.");

// https://schema.org/MusicEvent
t("MusicEvent");
t("Event type: Music event.");

// https://schema.org/MusicGroup
t("MusicGroup");
t("A musical group, such as a band, an orchestra, or a choir. Can also be a solo musician.");
t("A musical group, such as a band, an orchestra, or a choir.");

// https://schema.org/MusicPlaylist
t("MusicPlaylist");
t("A collection of music tracks in playlist form.");

// https://schema.org/MusicRecording
t("MusicRecording");
t("A music recording (track), usually a single song.");

// https://schema.org/MusicRelease
t("MusicRelease");
t("A MusicRelease is a specific release of a music album.");

// https://schema.org/MusicReleaseFormatType
t("MusicReleaseFormatType");
t("Format of this release (the type of recording media used, i.e. compact disc, digital media, LP, etc.).");

// https://schema.org/MusicStore
t("MusicStore");
t("A music store.");

// https://schema.org/MusicVenue
t("MusicVenue");
t("A music venue.");

// https://schema.org/MusicVideoObject
t("MusicVideoObject");
t("A music video file.");

// https://schema.org/NailSalon
t("NailSalon");
t("A nail salon.");

// https://schema.org/NarcoticConsideration
t("NarcoticConsideration");
t('Item is a narcotic as defined by the <a href="https://www.incb.org/incb/en/narcotic-drugs/Yellowlist/yellow-list.html">1961 UN convention</a>, for example marijuana or heroin.');

// https://schema.org/Neck
t("Neck");
t("Neck assessment with clinical examination.");

// https://schema.org/NegativeFilmDigitalSource
t("NegativeFilmDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/negativeFilm">negative film</a></a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/Nerve
t("Nerve");
t("A common pathway for the electrochemical nerve impulses that are transmitted along each of the axons.");

// https://schema.org/Neuro
t("Neuro");
t("Neurological system clinical examination.");

// https://schema.org/Neurologic
t("Neurologic");
t("A specific branch of medical science that studies the nerves and nervous system and its respective disease states.");

// https://schema.org/NewCondition
t("NewCondition");
t("Indicates that the item is new.");

// https://schema.org/NewsArticle
t("NewsArticle");
t('A NewsArticle is an article whose content reports news, or provides background context and supporting materials for understanding the news.<br/><br/>

A more detailed overview of <a href="/docs/news.html">schema.org News markup</a> is also available.');
t("A NewsArticle is an article whose content reports news, or provides background context and supporting materials for understanding the news.");

// https://schema.org/NewsMediaOrganization
t("NewsMediaOrganization");
t("A News/Media organization such as a newspaper or TV station.");

// https://schema.org/Newspaper
t("Newspaper");
t("A publication containing information about varied topics that are pertinent to general information, a geographic area, or a specific subject matter (i.e. business, culture, education). Often published daily.");
t("A publication containing information about varied topics that are pertinent to general information, a geographic area, or a specific subject matter (i.e. business, culture, education).");

// https://schema.org/NGO
t("NGO");
t("Organization: Non-governmental Organization.");

// https://schema.org/NightClub
t("NightClub");
t("A nightclub or discotheque.");

// https://schema.org/NLNonprofitType
t("NLNonprofitType");
t("NLNonprofitType: Non-profit organization type originating from the Netherlands.");

// https://schema.org/NoninvasiveProcedure
t("NoninvasiveProcedure");
t("A type of medical procedure that involves noninvasive techniques.");

// https://schema.org/Nonprofit501a
t("Nonprofit501a");
t("Nonprofit501a: Non-profit type referring to Farmers’ Cooperative Associations.");

// https://schema.org/Nonprofit501c1
t("Nonprofit501c1");
t("Nonprofit501c1: Non-profit type referring to Corporations Organized Under Act of Congress, including Federal Credit Unions and National Farm Loan Associations.");

// https://schema.org/Nonprofit501c10
t("Nonprofit501c10");
t("Nonprofit501c10: Non-profit type referring to Domestic Fraternal Societies and Associations.");

// https://schema.org/Nonprofit501c11
t("Nonprofit501c11");
t("Nonprofit501c11: Non-profit type referring to Teachers' Retirement Fund Associations.");

// https://schema.org/Nonprofit501c12
t("Nonprofit501c12");
t("Nonprofit501c12: Non-profit type referring to Benevolent Life Insurance Associations, Mutual Ditch or Irrigation Companies, Mutual or Cooperative Telephone Companies.");

// https://schema.org/Nonprofit501c13
t("Nonprofit501c13");
t("Nonprofit501c13: Non-profit type referring to Cemetery Companies.");

// https://schema.org/Nonprofit501c14
t("Nonprofit501c14");
t("Nonprofit501c14: Non-profit type referring to State-Chartered Credit Unions, Mutual Reserve Funds.");

// https://schema.org/Nonprofit501c15
t("Nonprofit501c15");
t("Nonprofit501c15: Non-profit type referring to Mutual Insurance Companies or Associations.");

// https://schema.org/Nonprofit501c16
t("Nonprofit501c16");
t("Nonprofit501c16: Non-profit type referring to Cooperative Organizations to Finance Crop Operations.");

// https://schema.org/Nonprofit501c17
t("Nonprofit501c17");
t("Nonprofit501c17: Non-profit type referring to Supplemental Unemployment Benefit Trusts.");

// https://schema.org/Nonprofit501c18
t("Nonprofit501c18");
t("Nonprofit501c18: Non-profit type referring to Employee Funded Pension Trust (created before 25 June 1959).");

// https://schema.org/Nonprofit501c19
t("Nonprofit501c19");
t("Nonprofit501c19: Non-profit type referring to Post or Organization of Past or Present Members of the Armed Forces.");

// https://schema.org/Nonprofit501c2
t("Nonprofit501c2");
t("Nonprofit501c2: Non-profit type referring to Title-holding Corporations for Exempt Organizations.");

// https://schema.org/Nonprofit501c20
t("Nonprofit501c20");
t("Nonprofit501c20: Non-profit type referring to Group Legal Services Plan Organizations.");

// https://schema.org/Nonprofit501c21
t("Nonprofit501c21");
t("Nonprofit501c21: Non-profit type referring to Black Lung Benefit Trusts.");

// https://schema.org/Nonprofit501c22
t("Nonprofit501c22");
t("Nonprofit501c22: Non-profit type referring to Withdrawal Liability Payment Funds.");

// https://schema.org/Nonprofit501c23
t("Nonprofit501c23");
t("Nonprofit501c23: Non-profit type referring to Veterans Organizations.");

// https://schema.org/Nonprofit501c24
t("Nonprofit501c24");
t("Nonprofit501c24: Non-profit type referring to Section 4049 ERISA Trusts.");

// https://schema.org/Nonprofit501c25
t("Nonprofit501c25");
t("Nonprofit501c25: Non-profit type referring to Real Property Title-Holding Corporations or Trusts with Multiple Parents.");

// https://schema.org/Nonprofit501c26
t("Nonprofit501c26");
t("Nonprofit501c26: Non-profit type referring to State-Sponsored Organizations Providing Health Coverage for High-Risk Individuals.");

// https://schema.org/Nonprofit501c27
t("Nonprofit501c27");
t("Nonprofit501c27: Non-profit type referring to State-Sponsored Workers' Compensation Reinsurance Organizations.");

// https://schema.org/Nonprofit501c28
t("Nonprofit501c28");
t("Nonprofit501c28: Non-profit type referring to National Railroad Retirement Investment Trusts.");

// https://schema.org/Nonprofit501c3
t("Nonprofit501c3");
t("Nonprofit501c3: Non-profit type referring to Religious, Educational, Charitable, Scientific, Literary, Testing for Public Safety, Fostering National or International Amateur Sports Competition, or Prevention of Cruelty to Children or Animals Organizations.");

// https://schema.org/Nonprofit501c4
t("Nonprofit501c4");
t("Nonprofit501c4: Non-profit type referring to Civic Leagues, Social Welfare Organizations, and Local Associations of Employees.");

// https://schema.org/Nonprofit501c5
t("Nonprofit501c5");
t("Nonprofit501c5: Non-profit type referring to Labor, Agricultural and Horticultural Organizations.");

// https://schema.org/Nonprofit501c6
t("Nonprofit501c6");
t("Nonprofit501c6: Non-profit type referring to Business Leagues, Chambers of Commerce, Real Estate Boards.");

// https://schema.org/Nonprofit501c7
t("Nonprofit501c7");
t("Nonprofit501c7: Non-profit type referring to Social and Recreational Clubs.");

// https://schema.org/Nonprofit501c8
t("Nonprofit501c8");
t("Nonprofit501c8: Non-profit type referring to Fraternal Beneficiary Societies and Associations.");

// https://schema.org/Nonprofit501c9
t("Nonprofit501c9");
t("Nonprofit501c9: Non-profit type referring to Voluntary Employee Beneficiary Associations.");

// https://schema.org/Nonprofit501d
t("Nonprofit501d");
t("Nonprofit501d: Non-profit type referring to Religious and Apostolic Associations.");

// https://schema.org/Nonprofit501e
t("Nonprofit501e");
t("Nonprofit501e: Non-profit type referring to Cooperative Hospital Service Organizations.");

// https://schema.org/Nonprofit501f
t("Nonprofit501f");
t("Nonprofit501f: Non-profit type referring to Cooperative Service Organizations.");

// https://schema.org/Nonprofit501k
t("Nonprofit501k");
t("Nonprofit501k: Non-profit type referring to Child Care Organizations.");

// https://schema.org/Nonprofit501n
t("Nonprofit501n");
t("Nonprofit501n: Non-profit type referring to Charitable Risk Pools.");

// https://schema.org/Nonprofit501q
t("Nonprofit501q");
t("Nonprofit501q: Non-profit type referring to Credit Counseling Organizations.");

// https://schema.org/Nonprofit527
t("Nonprofit527");
t("Nonprofit527: Non-profit type referring to political organizations.");

// https://schema.org/NonprofitANBI
t("NonprofitANBI");
t("NonprofitANBI: Non-profit type referring to a Public Benefit Organization (NL).");

// https://schema.org/NonprofitSBBI
t("NonprofitSBBI");
t("NonprofitSBBI: Non-profit type referring to a Social Interest Promoting Institution (NL).");

// https://schema.org/NonprofitType
t("NonprofitType");
t("NonprofitType enumerates several kinds of official non-profit types of which a non-profit organization can be.");

// https://schema.org/Nose
t("Nose");
t("Nose function assessment with clinical examination.");

// https://schema.org/Notary
t("Notary");
t("A notary.");

// https://schema.org/NoteDigitalDocument
t("NoteDigitalDocument");
t("A file containing a note, primarily for the author.");

// https://schema.org/NotInForce
t("NotInForce");
t("Indicates that a legislation is currently not in force.");

// https://schema.org/NotYetRecruiting
t("NotYetRecruiting");
t("Not yet recruiting.");

// https://schema.org/Number
t("Number");
t("Data type: Number.<br/><br/>

Usage guidelines:<br/><br/>

<ul>
<li>Use values from 0123456789 (Unicode 'DIGIT ZERO' (U+0030) to 'DIGIT NINE' (U+0039)) rather than superficially similar Unicode symbols.</li>
<li>Use '.' (Unicode 'FULL STOP' (U+002E)) rather than ',' to indicate a decimal point. Avoid using these symbols as a readability separator.</li>
</ul>");
t("Data type: Number.");

// https://schema.org/Nursing
t("Nursing");
t("A health profession of a person formally educated and trained in the care of the sick or infirm person.");

// https://schema.org/NutritionInformation
t("NutritionInformation");
t("Nutritional information about the recipe.");

// https://schema.org/Observation
t("Observation");
t('Instances of the class <a class="localLink" href="/Observation">Observation</a> are used to specify observations about an entity at a particular time. The principal properties of an <a class="localLink" href="/Observation">Observation</a> are <a class="localLink" href="/observationAbout">observationAbout</a>, <a class="localLink" href="/measuredProperty">measuredProperty</a>, <a class="localLink" href="/statType">statType</a>, [[value] and <a class="localLink" href="/observationDate">observationDate</a>  and <a class="localLink" href="/measuredProperty">measuredProperty</a>. Some but not all Observations represent a <a class="localLink" href="/QuantitativeValue">QuantitativeValue</a>. Quantitative observations can be about a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>, which is an abstract specification about which we can make observations that are grounded at a particular location and time. <br/><br/>

Observations can also encode a subset of simple RDF-like statements (its observationAbout, a StatisticalVariable, defining the measuredPoperty; its observationAbout property indicating the entity the statement is about, and <a class="localLink" href="/value">value</a> )<br/><br/>

In the context of a quantitative knowledge graph, typical properties could include <a class="localLink" href="/measuredProperty">measuredProperty</a>, <a class="localLink" href="/observationAbout">observationAbout</a>, <a class="localLink" href="/observationDate">observationDate</a>, <a class="localLink" href="/value">value</a>, <a class="localLink" href="/unitCode">unitCode</a>, <a class="localLink" href="/unitText">unitText</a>, <a class="localLink" href="/measurementMethod">measurementMethod</a>.');
t('Instances of the class <a class="localLink" href="/Observation">Observation</a> are used to specify observations about an entity at a particular time.');

// https://schema.org/Observational
t("Observational");
t("An observational study design.");

// https://schema.org/Obstetric
t("Obstetric");
t("A specific branch of medical science that specializes in the care of women during the prenatal and postnatal care and with the delivery of the child.");

// https://schema.org/Occupation
t("Occupation");
t("A profession, may involve prolonged training and/or a formal qualification.");

// https://schema.org/OccupationalActivity
t("OccupationalActivity");
t("Any physical activity engaged in for job-related purposes. Examples may include waiting tables, maid service, carrying a mailbag, picking fruits or vegetables, construction work, etc.");
t("Any physical activity engaged in for job-related purposes.");

// https://schema.org/OccupationalExperienceRequirements
t("OccupationalExperienceRequirements");
t('Indicates employment-related experience requirements, e.g. <a class="localLink" href="/monthsOfExperience">monthsOfExperience</a>.');

// https://schema.org/OccupationalTherapy
t("OccupationalTherapy");
t("A treatment of people with physical, emotional, or social problems, using purposeful activity to help them overcome or learn to deal with their problems.");

// https://schema.org/OceanBodyOfWater
t("OceanBodyOfWater");
t("An ocean (for example, the Pacific).");

// https://schema.org/Offer
t("Offer");
t('An offer to transfer some rights to an item or to provide a service — for example, an offer to sell tickets to an event, to rent the DVD of a movie, to stream a TV show over the internet, to repair a motorcycle, or to loan a book.<br/><br/>

Note: As the <a class="localLink" href="/businessFunction">businessFunction</a> property, which identifies the form of offer (e.g. sell, lease, repair, dispose), defaults to http://purl.org/goodrelations/v1#Sell; an Offer without a defined businessFunction value can be assumed to be an offer to sell.<br/><br/>

For <a href="http://www.gs1.org/barcodes/technical/idkeys/gtin">GTIN</a>-related fields, see <a href="http://www.gs1.org/barcodes/support/check_digit_calculator">Check Digit calculator</a> and <a href="http://www.gs1us.org/resources/standards/gtin-validation-guide">validation guide</a> from <a href="http://www.gs1.org/">GS1</a>.');
t("An offer to transfer some rights to an item or to provide a service — for example, an offer to sell tickets to an event, to rent the DVD of a movie, to stream a TV show over the internet, to repair a motorcycle, or to loan a book.");

// https://schema.org/OfferCatalog
t("OfferCatalog");
t("An OfferCatalog is an ItemList that contains related Offers and/or further OfferCatalogs that are offeredBy the same provider.");

// https://schema.org/OfferForLease
t("OfferForLease");
t('An <a class="localLink" href="/OfferForLease">OfferForLease</a> in Schema.org represents an <a class="localLink" href="/Offer">Offer</a> to lease out something, i.e. an <a class="localLink" href="/Offer">Offer</a> whose
  <a class="localLink" href="/businessFunction">businessFunction</a> is <a href="http://purl.org/goodrelations/v1#LeaseOut.">lease out</a>. See <a href="https://en.wikipedia.org/wiki/GoodRelations">Good Relations</a> for
  background on the underlying concepts.');
t('An <a class="localLink" href="/OfferForLease">OfferForLease</a> in Schema.org represents an <a class="localLink" href="/Offer">Offer</a> to lease out something, i.e. an <a class="localLink" href="/Offer">Offer</a> whose
  <a class="localLink" href="/businessFunction">businessFunction</a> is <a href="http://purl.org/goodrelations/v1#LeaseOut.">lease out</a>.');

// https://schema.org/OfferForPurchase
t("OfferForPurchase");
t('An <a class="localLink" href="/OfferForPurchase">OfferForPurchase</a> in Schema.org represents an <a class="localLink" href="/Offer">Offer</a> to sell something, i.e. an <a class="localLink" href="/Offer">Offer</a> whose
  <a class="localLink" href="/businessFunction">businessFunction</a> is <a href="http://purl.org/goodrelations/v1#Sell.">sell</a>. See <a href="https://en.wikipedia.org/wiki/GoodRelations">Good Relations</a> for
  background on the underlying concepts.');
t('An <a class="localLink" href="/OfferForPurchase">OfferForPurchase</a> in Schema.org represents an <a class="localLink" href="/Offer">Offer</a> to sell something, i.e. an <a class="localLink" href="/Offer">Offer</a> whose
  <a class="localLink" href="/businessFunction">businessFunction</a> is <a href="http://purl.org/goodrelations/v1#Sell.">sell</a>.');

// https://schema.org/OfferItemCondition
t("OfferItemCondition");
t("A list of possible conditions for the item.");

// https://schema.org/OfferShippingDetails
t("OfferShippingDetails");
t("OfferShippingDetails represents information about shipping destinations.<br/><br/>

Multiple of these entities can be used to represent different shipping rates for different destinations:<br/><br/>

One entity for Alaska/Hawaii. A different one for continental US. A different one for all France.<br/><br/>

Multiple of these entities can be used to represent different shipping costs and delivery times.<br/><br/>

Two entities that are identical but differ in rate and time:<br/><br/>

E.g. Cheaper and slower: $5 in 5-7 days
or Fast and expensive: $15 in 1-2 days.");
t("OfferShippingDetails represents information about shipping destinations.");

// https://schema.org/OfficeEquipmentStore
t("OfficeEquipmentStore");
t("An office equipment store.");

// https://schema.org/OfficialLegalValue
t("OfficialLegalValue");
t('All the documents published by an official publisher should have at least the legal value level "OfficialLegalValue". This indicates that the document was published by an organisation with the public task of making it available (e.g. a consolidated version of an EU directive published by the EU Office of Publications).');
t('All the documents published by an official publisher should have at least the legal value level "OfficialLegalValue".');

// https://schema.org/OfflineEventAttendanceMode
t("OfflineEventAttendanceMode");
t("OfflineEventAttendanceMode - an event that is primarily conducted offline.");

// https://schema.org/OfflinePermanently
t("OfflinePermanently");
t("Game server status: OfflinePermanently. Server is offline and not available.");
t("Game server status: OfflinePermanently.");

// https://schema.org/OfflineTemporarily
t("OfflineTemporarily");
t("Game server status: OfflineTemporarily. Server is offline now but it can be online soon.");
t("Game server status: OfflineTemporarily.");

// https://schema.org/Oncologic
t("Oncologic");
t("A specific branch of medical science that deals with benign and malignant tumors, including the study of their development, diagnosis, treatment and prevention.");

// https://schema.org/OnDemandEvent
t("OnDemandEvent");
t("A publication event, e.g. catch-up TV or radio podcast, during which a program is available on-demand.");

// https://schema.org/OneTimePayments
t("OneTimePayments");
t("OneTimePayments: this is a benefit for one-time payments for individuals.");

// https://schema.org/Online
t("Online");
t("Game server status: Online. Server is available.");
t("Game server status: Online.");

// https://schema.org/OnlineBusiness
t("OnlineBusiness");
t("A particular online business, either standalone or the online part of a broader organization. Examples include an eCommerce site, an online travel booking site, an online learning site, an online logistics and shipping provider, an online (virtual) doctor, etc.");
t("A particular online business, either standalone or the online part of a broader organization.");

// https://schema.org/OnlineEventAttendanceMode
t("OnlineEventAttendanceMode");
t("OnlineEventAttendanceMode - an event that is primarily conducted online.");

// https://schema.org/OnlineFull
t("OnlineFull");
t("Game server status: OnlineFull. Server is online but unavailable. The maximum number of players has reached.");
t("Game server status: OnlineFull.");

// https://schema.org/OnlineOnly
t("OnlineOnly");
t("Indicates that the item is available only online.");

// https://schema.org/OnlineStore
t("OnlineStore");
t("An eCommerce site.");

// https://schema.org/OnSitePickup
t("OnSitePickup");
t("A DeliveryMethod in which an item is collected on site, e.g. in a store or at a box office.");

// https://schema.org/OpeningHoursSpecification
t("OpeningHoursSpecification");
t('A structured value providing information about the opening hours of a place or a certain service inside a place.<br/><br/>

The place is <strong>open</strong> if the <a class="localLink" href="/opens">opens</a> property is specified, and <strong>closed</strong> otherwise.<br/><br/>

If the value for the <a class="localLink" href="/closes">closes</a> property is less than the value for the <a class="localLink" href="/opens">opens</a> property then the hour range is assumed to span over the next day.');
t("A structured value providing information about the opening hours of a place or a certain service inside a place.");

// https://schema.org/OpenTrial
t("OpenTrial");
t("A trial design in which the researcher knows the full details of the treatment, and so does the patient.");

// https://schema.org/OpinionNewsArticle
t("OpinionNewsArticle");
t('An <a class="localLink" href="/OpinionNewsArticle">OpinionNewsArticle</a> is a <a class="localLink" href="/NewsArticle">NewsArticle</a> that primarily expresses opinions rather than journalistic reporting of news and events. For example, a <a class="localLink" href="/NewsArticle">NewsArticle</a> consisting of a column or <a class="localLink" href="/Blog">Blog</a>/<a class="localLink" href="/BlogPosting">BlogPosting</a> entry in the Opinions section of a news publication.');
t('An <a class="localLink" href="/OpinionNewsArticle">OpinionNewsArticle</a> is a <a class="localLink" href="/NewsArticle">NewsArticle</a> that primarily expresses opinions rather than journalistic reporting of news and events.');

// https://schema.org/Optician
t("Optician");
t("A store that sells reading glasses and similar devices for improving vision.");

// https://schema.org/Optometric
t("Optometric");
t("The science or practice of testing visual acuity and prescribing corrective lenses.");

// https://schema.org/Order
t("Order");
t("An order is a confirmation of a transaction (a receipt), which can contain multiple line items, each represented by an Offer that has been accepted by the customer.");

// https://schema.org/OrderAction
t("OrderAction");
t("An agent orders an object/product/service to be delivered/sent.");

// https://schema.org/OrderCancelled
t("OrderCancelled");
t("OrderStatus representing cancellation of an order.");

// https://schema.org/OrderDelivered
t("OrderDelivered");
t("OrderStatus representing successful delivery of an order.");

// https://schema.org/OrderInTransit
t("OrderInTransit");
t("OrderStatus representing that an order is in transit.");

// https://schema.org/OrderItem
t("OrderItem");
t("An order item is a line of an order. It includes the quantity and shipping details of a bought offer.");
t("An order item is a line of an order.");

// https://schema.org/OrderPaymentDue
t("OrderPaymentDue");
t("OrderStatus representing that payment is due on an order.");

// https://schema.org/OrderPickupAvailable
t("OrderPickupAvailable");
t("OrderStatus representing availability of an order for pickup.");

// https://schema.org/OrderProblem
t("OrderProblem");
t("OrderStatus representing that there is a problem with the order.");

// https://schema.org/OrderProcessing
t("OrderProcessing");
t("OrderStatus representing that an order is being processed.");

// https://schema.org/OrderReturned
t("OrderReturned");
t("OrderStatus representing that an order has been returned.");

// https://schema.org/OrderStatus
t("OrderStatus");
t("Enumerated status values for Order.");

// https://schema.org/Organization
t("Organization");
t("An organization such as a school, NGO, corporation, club, etc.");

// https://schema.org/OrganizationRole
t("OrganizationRole");
t("A subclass of Role used to describe roles within organizations.");

// https://schema.org/OrganizeAction
t("OrganizeAction");
t("The act of manipulating/administering/supervising/controlling one or more objects.");

// https://schema.org/OriginalMediaContent
t("OriginalMediaContent");
t('Content coded \'as original media content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.<br/><br/>

For a <a class="localLink" href="/VideoObject">VideoObject</a> to be \'original\': No evidence the footage has been misleadingly altered or manipulated, though it may contain false or misleading claims.<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> to be \'original\': No evidence the image has been misleadingly altered or manipulated, though it may still contain false or misleading claims.<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> with embedded text to be \'original\': No evidence the image has been misleadingly altered or manipulated, though it may still contain false or misleading claims.<br/><br/>

For an <a class="localLink" href="/AudioObject">AudioObject</a> to be \'original\': No evidence the audio has been misleadingly altered or manipulated, though it may contain false or misleading claims.');
t('Content coded \'as original media content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.');

// https://schema.org/OriginalShippingFees
t("OriginalShippingFees");
t("Specifies that the customer must pay the original shipping costs when returning a product.");

// https://schema.org/Osteopathic
t("Osteopathic");
t("A system of medicine focused on promoting the body's innate ability to heal itself.");

// https://schema.org/OTC
t("OTC");
t("The character of a medical substance, typically a medicine, of being available over the counter or not.");

// https://schema.org/Otolaryngologic
t("Otolaryngologic");
t("A specific branch of medical science that is concerned with the ear, nose and throat and their respective disease states.");

// https://schema.org/OutletStore
t("OutletStore");
t("An outlet store.");

// https://schema.org/OutOfStock
t("OutOfStock");
t("Indicates that the item is out of stock.");

// https://schema.org/OverviewHealthAspect
t("OverviewHealthAspect");
t("Overview of the content. Contains a summarized view of the topic with the most relevant information for an introduction.");
t("Overview of the content.");

// https://schema.org/OwnershipInfo
t("OwnershipInfo");
t("A structured value providing information about when a certain organization or person owned a certain product.");

// https://schema.org/PaidLeave
t("PaidLeave");
t("PaidLeave: this is a benefit for paid leave.");

// https://schema.org/PaintAction
t("PaintAction");
t("The act of producing a painting, typically with paint and canvas as instruments.");

// https://schema.org/Painting
t("Painting");
t("A painting.");

// https://schema.org/PalliativeProcedure
t("PalliativeProcedure");
t("A medical procedure intended primarily for palliative purposes, aimed at relieving the symptoms of an underlying health condition.");

// https://schema.org/Paperback
t("Paperback");
t("Book format: Paperback.");

// https://schema.org/ParcelDelivery
t("ParcelDelivery");
t("The delivery of a parcel either via the postal service or a commercial service.");

// https://schema.org/ParcelService
t("ParcelService");
t("A private parcel service as the delivery mode available for a certain offer.<br/><br/>

Commonly used values:<br/><br/>

<ul>
<li>http://purl.org/goodrelations/v1#DHL</li>
<li>http://purl.org/goodrelations/v1#FederalExpress</li>
<li>http://purl.org/goodrelations/v1#UPS</li>
</ul>");
t("A private parcel service as the delivery mode available for a certain offer.");

// https://schema.org/ParentalSupport
t("ParentalSupport");
t("ParentalSupport: this is a benefit for parental support.");

// https://schema.org/ParentAudience
t("ParentAudience");
t("A set of characteristics describing parents, who can be interested in viewing some content.");

// https://schema.org/Park
t("Park");
t("A park.");

// https://schema.org/ParkingFacility
t("ParkingFacility");
t("A parking lot or other parking facility.");

// https://schema.org/ParkingMap
t("ParkingMap");
t("A parking map.");

// https://schema.org/PartiallyInForce
t("PartiallyInForce");
t("Indicates that parts of the legislation are in force, and parts are not.");

// https://schema.org/Pathology
t("Pathology");
t("A specific branch of medical science that is concerned with the study of the cause, origin and nature of a disease state, including its consequences as a result of manifestation of the disease. In clinical care, the term is used to designate a branch of medicine using laboratory tests to diagnose and determine the prognostic significance of illness.");
t("A specific branch of medical science that is concerned with the study of the cause, origin and nature of a disease state, including its consequences as a result of manifestation of the disease.");

// https://schema.org/PathologyTest
t("PathologyTest");
t("A medical test performed by a laboratory that typically involves examination of a tissue sample by a pathologist.");

// https://schema.org/Patient
t("Patient");
t("A patient is any person recipient of health care services.");

// https://schema.org/PatientExperienceHealthAspect
t("PatientExperienceHealthAspect");
t("Content about the real life experience of patients or people that have lived a similar experience about the topic. May be forums, topics, Q-and-A and related material.");
t("Content about the real life experience of patients or people that have lived a similar experience about the topic.");

// https://schema.org/PawnShop
t("PawnShop");
t("A shop that will buy, or lend money against the security of, personal possessions.");

// https://schema.org/PayAction
t("PayAction");
t("An agent pays a price to a participant.");

// https://schema.org/PaymentAutomaticallyApplied
t("PaymentAutomaticallyApplied");
t("An automatic payment system is in place and will be used.");

// https://schema.org/PaymentCard
t("PaymentCard");
t("A payment method using a credit, debit, store or other card to associate the payment with an account.");

// https://schema.org/PaymentChargeSpecification
t("PaymentChargeSpecification");
t("The costs of settling the payment using a particular payment method.");

// https://schema.org/PaymentComplete
t("PaymentComplete");
t("The payment has been received and processed.");

// https://schema.org/PaymentDeclined
t("PaymentDeclined");
t("The payee received the payment, but it was declined for some reason.");

// https://schema.org/PaymentDue
t("PaymentDue");
t("The payment is due, but still within an acceptable time to be received.");

// https://schema.org/PaymentMethod
t("PaymentMethod");
t("A payment method is a standardized procedure for transferring the monetary amount for a purchase. Payment methods are characterized by the legal and technical structures used, and by the organization or group carrying out the transaction.<br/><br/>

Commonly used values:<br/><br/>

<ul>
<li>http://purl.org/goodrelations/v1#ByBankTransferInAdvance</li>
<li>http://purl.org/goodrelations/v1#ByInvoice</li>
<li>http://purl.org/goodrelations/v1#Cash</li>
<li>http://purl.org/goodrelations/v1#CheckInAdvance</li>
<li>http://purl.org/goodrelations/v1#COD</li>
<li>http://purl.org/goodrelations/v1#DirectDebit</li>
<li>http://purl.org/goodrelations/v1#GoogleCheckout</li>
<li>http://purl.org/goodrelations/v1#PayPal</li>
<li>http://purl.org/goodrelations/v1#PaySwarm</li>
</ul>");
t("A payment method is a standardized procedure for transferring the monetary amount for a purchase.");

// https://schema.org/PaymentPastDue
t("PaymentPastDue");
t("The payment is due and considered late.");

// https://schema.org/PaymentService
t("PaymentService");
t("A Service to transfer funds from a person or organization to a beneficiary person or organization.");

// https://schema.org/PaymentStatusType
t("PaymentStatusType");
t("A specific payment status. For example, PaymentDue, PaymentComplete, etc.");
t("A specific payment status.");

// https://schema.org/Pediatric
t("Pediatric");
t("A specific branch of medical science that specializes in the care of infants, children and adolescents.");

// https://schema.org/PeopleAudience
t("PeopleAudience");
t("A set of characteristics belonging to people, e.g. who compose an item's target audience.");

// https://schema.org/PercutaneousProcedure
t("PercutaneousProcedure");
t("A type of medical procedure that involves percutaneous techniques, where access to organs or tissue is achieved via needle-puncture of the skin. For example, catheter-based procedures like stent delivery.");
t("A type of medical procedure that involves percutaneous techniques, where access to organs or tissue is achieved via needle-puncture of the skin.");

// https://schema.org/PerformAction
t("PerformAction");
t("The act of participating in performance arts.");

// https://schema.org/PerformanceRole
t("PerformanceRole");
t("A PerformanceRole is a Role that some entity places with regard to a theatrical performance, e.g. in a Movie, TVSeries etc.");

// https://schema.org/PerformingArtsTheater
t("PerformingArtsTheater");
t("A theater or other performing art center.");

// https://schema.org/PerformingGroup
t("PerformingGroup");
t("A performance group, such as a band, an orchestra, or a circus.");

// https://schema.org/Periodical
t("Periodical");
t('A publication in any medium issued in successive parts bearing numerical or chronological designations and intended to continue indefinitely, such as a magazine, scholarly journal, or newspaper.<br/><br/>

See also <a href="http://blog.schema.org/2014/09/schemaorg-support-for-bibliographic_2.html">blog post</a>.');
t("A publication in any medium issued in successive parts bearing numerical or chronological designations and intended to continue indefinitely, such as a magazine, scholarly journal, or newspaper.");

// https://schema.org/Permit
t("Permit");
t("A permit issued by an organization, e.g. a parking pass.");

// https://schema.org/Person
t("Person");
t("A person (alive, dead, undead, or fictional).");

// https://schema.org/PET
t("PET");
t("Positron emission tomography imaging.");

// https://schema.org/PetStore
t("PetStore");
t("A pet store.");

// https://schema.org/Pharmacy
t("Pharmacy");
t("A pharmacy or drugstore.");

// https://schema.org/PharmacySpecialty
t("PharmacySpecialty");
t("The practice or art and science of preparing and dispensing drugs and medicines.");

// https://schema.org/Photograph
t("Photograph");
t("A photograph.");

// https://schema.org/PhotographAction
t("PhotographAction");
t("The act of capturing still images of objects using a camera.");

// https://schema.org/PhysicalActivity
t("PhysicalActivity");
t("Any bodily activity that enhances or maintains physical fitness and overall health and wellness. Includes activity that is part of daily living and routine, structured exercise, and exercise prescribed as part of a medical treatment or recovery plan.");
t("Any bodily activity that enhances or maintains physical fitness and overall health and wellness.");

// https://schema.org/PhysicalActivityCategory
t("PhysicalActivityCategory");
t("Categories of physical activity, organized by physiologic classification.");

// https://schema.org/PhysicalExam
t("PhysicalExam");
t("A type of physical examination of a patient performed by a physician.");

// https://schema.org/PhysicalTherapy
t("PhysicalTherapy");
t("A process of progressive physical care and rehabilitation aimed at improving a health condition.");

// https://schema.org/Physician
t("Physician");
t('An individual physician or a physician\'s office considered as a <a class="localLink" href="/MedicalOrganization">MedicalOrganization</a>.');

// https://schema.org/PhysiciansOffice
t("PhysiciansOffice");
t("A doctor's office or clinic.");

// https://schema.org/Physiotherapy
t("Physiotherapy");
t("The practice of treatment of disease, injury, or deformity by physical methods such as massage, heat treatment, and exercise rather than by drugs or surgery.");

// https://schema.org/Place
t("Place");
t("Entities that have a somewhat fixed, physical extension.");

// https://schema.org/PlaceboControlledTrial
t("PlaceboControlledTrial");
t("A placebo-controlled trial design.");

// https://schema.org/PlaceOfWorship
t("PlaceOfWorship");
t("Place of worship, such as a church, synagogue, or mosque.");

// https://schema.org/PlanAction
t("PlanAction");
t("The act of planning the execution of an event/task/action/reservation/plan to a future date.");

// https://schema.org/PlasticSurgery
t("PlasticSurgery");
t("A specific branch of medical science that pertains to therapeutic or cosmetic repair or re-formation of missing, injured or malformed tissues or body parts by manual and instrumental means.");

// https://schema.org/Play
t("Play");
t('A play is a form of literature, usually consisting of dialogue between characters, intended for theatrical performance rather than just reading. Note: A performance of a Play would be a <a class="localLink" href="/TheaterEvent">TheaterEvent</a> or <a class="localLink" href="/BroadcastEvent">BroadcastEvent</a> - the <em>Play</em> being the <a class="localLink" href="/workPerformed">workPerformed</a>.');
t("A play is a form of literature, usually consisting of dialogue between characters, intended for theatrical performance rather than just reading.");

// https://schema.org/PlayAction
t("PlayAction");
t('The act of playing/exercising/training/performing for enjoyment, leisure, recreation, competition or exercise.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/ListenAction">ListenAction</a>: Unlike ListenAction (which is under ConsumeAction), PlayAction refers to performing for an audience or at an event, rather than consuming music.</li>
<li><a class="localLink" href="/WatchAction">WatchAction</a>: Unlike WatchAction (which is under ConsumeAction), PlayAction refers to showing/displaying for an audience or at an event, rather than consuming visual content.</li>
</ul>');
t("The act of playing/exercising/training/performing for enjoyment, leisure, recreation, competition or exercise.");

// https://schema.org/PlayGameAction
t("PlayGameAction");
t("The act of playing a video game.");

// https://schema.org/Playground
t("Playground");
t("A playground.");

// https://schema.org/Plumber
t("Plumber");
t("A plumbing service.");

// https://schema.org/PodcastEpisode
t("PodcastEpisode");
t("A single episode of a podcast series.");

// https://schema.org/PodcastSeason
t("PodcastSeason");
t("A single season of a podcast. Many podcasts do not break down into separate seasons. In that case, PodcastSeries should be used.");
t("A single season of a podcast.");

// https://schema.org/PodcastSeries
t("PodcastSeries");
t("A podcast is an episodic series of digital audio or video files which a user can download and listen to.");

// https://schema.org/Podiatric
t("Podiatric");
t("Podiatry is the care of the human foot, especially the diagnosis and treatment of foot disorders.");

// https://schema.org/PoliceStation
t("PoliceStation");
t("A police station.");

// https://schema.org/PoliticalParty
t("PoliticalParty");
t("Organization: Political Party.");

// https://schema.org/Pond
t("Pond");
t("A pond.");

// https://schema.org/PositiveFilmDigitalSource
t("PositiveFilmDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/positiveFilm">positive film</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/PostalAddress
t("PostalAddress");
t("The mailing address.");

// https://schema.org/PostalCodeRangeSpecification
t("PostalCodeRangeSpecification");
t('Indicates a range of postal codes, usually defined as the set of valid codes between <a class="localLink" href="/postalCodeBegin">postalCodeBegin</a> and <a class="localLink" href="/postalCodeEnd">postalCodeEnd</a>, inclusively.');

// https://schema.org/Poster
t("Poster");
t("A large, usually printed placard, bill, or announcement, often illustrated, that is posted to advertise or publicize something.");

// https://schema.org/PostOffice
t("PostOffice");
t("A post office.");

// https://schema.org/PotentialActionStatus
t("PotentialActionStatus");
t("A description of an action that is supported.");

// https://schema.org/PregnancyHealthAspect
t("PregnancyHealthAspect");
t("Content discussing pregnancy-related aspects of a health topic.");

// https://schema.org/PreOrder
t("PreOrder");
t("Indicates that the item is available for pre-order.");

// https://schema.org/PreOrderAction
t("PreOrderAction");
t("An agent orders a (not yet released) object/product/service to be delivered/sent.");

// https://schema.org/PrependAction
t("PrependAction");
t("The act of inserting at the beginning if an ordered collection.");

// https://schema.org/PreSale
t("PreSale");
t("Indicates that the item is available for ordering and delivery before general availability.");

// https://schema.org/Preschool
t("Preschool");
t("A preschool.");

// https://schema.org/PrescriptionOnly
t("PrescriptionOnly");
t("Available by prescription only.");

// https://schema.org/PresentationDigitalDocument
t("PresentationDigitalDocument");
t("A file containing slides or used for a presentation.");

// https://schema.org/PreventionHealthAspect
t("PreventionHealthAspect");
t("Information about actions or measures that can be taken to avoid getting the topic or reaching a critical situation related to the topic.");

// https://schema.org/PreventionIndication
t("PreventionIndication");
t("An indication for preventing an underlying condition, symptom, etc.");

// https://schema.org/PriceComponentTypeEnumeration
t("PriceComponentTypeEnumeration");
t("Enumerates different price components that together make up the total price for an offered product.");

// https://schema.org/PriceSpecification
t("PriceSpecification");
t('A structured value representing a price or price range. Typically, only the subclasses of this type are used for markup. It is recommended to use <a class="localLink" href="/MonetaryAmount">MonetaryAmount</a> to describe independent amounts of money such as a salary, credit card limits, etc.');
t("A structured value representing a price or price range.");

// https://schema.org/PriceTypeEnumeration
t("PriceTypeEnumeration");
t("Enumerates different price types, for example list price, invoice price, and sale price.");

// https://schema.org/PrimaryCare
t("PrimaryCare");
t("The medical care by a physician, or other health-care professional, who is the patient's first contact with the health-care system and who may recommend a specialist if necessary.");

// https://schema.org/PrintDigitalSource
t("PrintDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/print">print</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/Prion
t("Prion");
t("A prion is an infectious agent composed of protein in a misfolded form.");

// https://schema.org/Product
t("Product");
t("Any offered product or service. For example: a pair of shoes; a concert ticket; the rental of a car; a haircut; or an episode of a TV show streamed online.");
t("Any offered product or service.");

// https://schema.org/ProductCollection
t("ProductCollection");
t('A set of products (either <a class="localLink" href="/ProductGroup">ProductGroup</a>s or specific variants) that are listed together e.g. in an <a class="localLink" href="/Offer">Offer</a>.');

// https://schema.org/ProductGroup
t("ProductGroup");
t('A ProductGroup represents a group of <a class="localLink" href="/Product">Product</a>s that vary only in certain well-described ways, such as by <a class="localLink" href="/size">size</a>, <a class="localLink" href="/color">color</a>, <a class="localLink" href="/material">material</a> etc.<br/><br/>

While a ProductGroup itself is not directly offered for sale, the various varying products that it represents can be. The ProductGroup serves as a prototype or template, standing in for all of the products who have an <a class="localLink" href="/isVariantOf">isVariantOf</a> relationship to it. As such, properties (including additional types) can be applied to the ProductGroup to represent characteristics shared by each of the (possibly very many) variants. Properties that reference a ProductGroup are not included in this mechanism; neither are the following specific properties <a class="localLink" href="/variesBy">variesBy</a>, <a class="localLink" href="/hasVariant">hasVariant</a>, <a class="localLink" href="/url">url</a>.');
t('A ProductGroup represents a group of <a class="localLink" href="/Product">Product</a>s that vary only in certain well-described ways, such as by <a class="localLink" href="/size">size</a>, <a class="localLink" href="/color">color</a>, <a class="localLink" href="/material">material</a> etc.<br/><br/>

While a ProductGroup itself is not directly offered for sale, the various varying products that it represents can be.');

// https://schema.org/ProductModel
t("ProductModel");
t("A datasheet or vendor specification of a product (in the sense of a prototypical description).");

// https://schema.org/ProductReturnEnumeration
t("ProductReturnEnumeration");
t("ProductReturnEnumeration enumerates several kinds of product return policy. Note that this structure may not capture all aspects of the policy.");
t("ProductReturnEnumeration enumerates several kinds of product return policy.");

// https://schema.org/ProductReturnFiniteReturnWindow
t("ProductReturnFiniteReturnWindow");
t("ProductReturnFiniteReturnWindow: there is a finite window for product returns.");

// https://schema.org/ProductReturnNotPermitted
t("ProductReturnNotPermitted");
t("ProductReturnNotPermitted: product returns are not permitted.");

// https://schema.org/ProductReturnPolicy
t("ProductReturnPolicy");
t('A ProductReturnPolicy provides information about product return policies associated with an <a class="localLink" href="/Organization">Organization</a> or <a class="localLink" href="/Product">Product</a>.');

// https://schema.org/ProductReturnUnlimitedWindow
t("ProductReturnUnlimitedWindow");
t("ProductReturnUnlimitedWindow: there is an unlimited window for product returns.");

// https://schema.org/ProductReturnUnspecified
t("ProductReturnUnspecified");
t("ProductReturnUnspecified: a product return policy is not specified here.");

// https://schema.org/ProfessionalService
t("ProfessionalService");
t('Original definition: "provider of professional services."<br/><br/>

The general <a class="localLink" href="/ProfessionalService">ProfessionalService</a> type for local businesses was deprecated due to confusion with <a class="localLink" href="/Service">Service</a>. For reference, the types that it included were: <a class="localLink" href="/Dentist">Dentist</a>,
        <a class="localLink" href="/AccountingService">AccountingService</a>, <a class="localLink" href="/Attorney">Attorney</a>, <a class="localLink" href="/Notary">Notary</a>, as well as types for several kinds of <a class="localLink" href="/HomeAndConstructionBusiness">HomeAndConstructionBusiness</a>: <a class="localLink" href="/Electrician">Electrician</a>, <a class="localLink" href="/GeneralContractor">GeneralContractor</a>,
        <a class="localLink" href="/HousePainter">HousePainter</a>, <a class="localLink" href="/Locksmith">Locksmith</a>, <a class="localLink" href="/Plumber">Plumber</a>, <a class="localLink" href="/RoofingContractor">RoofingContractor</a>. <a class="localLink" href="/LegalService">LegalService</a> was introduced as a more inclusive supertype of <a class="localLink" href="/Attorney">Attorney</a>.');
t('Original definition: "provider of professional services.');

// https://schema.org/ProfilePage
t("ProfilePage");
t("Web page type: Profile page.");

// https://schema.org/PrognosisHealthAspect
t("PrognosisHealthAspect");
t("Typical progression and happenings of life course of the topic.");

// https://schema.org/ProgramMembership
t("ProgramMembership");
t('Used to describe membership in a loyalty programs (e.g. "StarAliance"), traveler clubs (e.g. "AAA"), purchase clubs ("Safeway Club"), etc.');

// https://schema.org/Project
t("Project");
t('An enterprise (potentially individual but typically collaborative), planned to achieve a particular aim.
Use properties from <a class="localLink" href="/Organization">Organization</a>, <a class="localLink" href="/subOrganization">subOrganization</a>/<a class="localLink" href="/parentOrganization">parentOrganization</a> to indicate project sub-structures.');
t("An enterprise (potentially individual but typically collaborative), planned to achieve a particular aim.");

// https://schema.org/PronounceableText
t("PronounceableText");
t("Data type: PronounceableText.");

// https://schema.org/Property
t("Property");
t("A property, used to indicate attributes and relationships of some Thing; equivalent to rdf:Property.");

// https://schema.org/PropertyValue
t("PropertyValue");
t("A property-value pair, e.g. representing a feature of a product or place. Use the 'name' property for the name of the property. If there is an additional human-readable version of the value, put that into the 'description' property.<br/><br/>

Always use specific schema.org properties when a) they exist and b) you can populate them. Using PropertyValue as a substitute will typically not trigger the same effect as using the original, specific property.");
t("A property-value pair, e.g. representing a feature of a product or place.");

// https://schema.org/PropertyValueSpecification
t("PropertyValueSpecification");
t("A Property value specification.");

// https://schema.org/Protein
t("Protein");
t("Protein is here used in its widest possible definition, as classes of amino acid based molecules. Amyloid-beta Protein in human (UniProt P05067), eukaryota (e.g. an OrthoDB group) or even a single molecule that one can point to are all of type :Protein. A protein can thus be a subclass of another protein, e.g. :Protein as a UniProt record can have multiple isoforms inside it which would also be :Protein. They can be imagined, synthetic, hypothetical or naturally occurring.");
t("Protein is here used in its widest possible definition, as classes of amino acid based molecules.");

// https://schema.org/Protozoa
t("Protozoa");
t("Single-celled organism that causes an infection.");

// https://schema.org/Psychiatric
t("Psychiatric");
t("A specific branch of medical science that is concerned with the study, treatment, and prevention of mental illness, using both medical and psychological therapies.");

// https://schema.org/PsychologicalTreatment
t("PsychologicalTreatment");
t("A process of care relying upon counseling, dialogue and communication  aimed at improving a mental health condition without use of drugs.");

// https://schema.org/PublicationEvent
t("PublicationEvent");
t("A PublicationEvent corresponds indifferently to the event of publication for a CreativeWork of any type, e.g. a broadcast event, an on-demand event, a book/journal publication via a variety of delivery media.");

// https://schema.org/PublicationIssue
t("PublicationIssue");
t('A part of a successively published publication such as a periodical or publication volume, often numbered, usually containing a grouping of works such as articles.<br/><br/>

See also <a href="http://blog.schema.org/2014/09/schemaorg-support-for-bibliographic_2.html">blog post</a>.');
t("A part of a successively published publication such as a periodical or publication volume, often numbered, usually containing a grouping of works such as articles.");

// https://schema.org/PublicationVolume
t("PublicationVolume");
t('A part of a successively published publication such as a periodical or multi-volume work, often numbered. It may represent a time span, such as a year.<br/><br/>

See also <a href="http://blog.schema.org/2014/09/schemaorg-support-for-bibliographic_2.html">blog post</a>.');
t("A part of a successively published publication such as a periodical or multi-volume work, often numbered.");

// https://schema.org/PublicHealth
t("PublicHealth");
t("Branch of medicine that pertains to the health services to improve and protect community health, especially epidemiology, sanitation, immunization, and preventive medicine.");

// https://schema.org/PublicHolidays
t("PublicHolidays");
t('This stands for any day that is a public holiday; it is a placeholder for all official public holidays in some particular location. While not technically a "day of the week", it can be used with <a class="localLink" href="/OpeningHoursSpecification">OpeningHoursSpecification</a>. In the context of an opening hours specification it can be used to indicate opening hours on public holidays, overriding general opening hours for the day of the week on which a public holiday occurs.');
t("This stands for any day that is a public holiday; it is a placeholder for all official public holidays in some particular location.");

// https://schema.org/PublicSwimmingPool
t("PublicSwimmingPool");
t("A public swimming pool.");

// https://schema.org/PublicToilet
t("PublicToilet");
t("A public toilet is a room or small building containing one or more toilets (and possibly also urinals) which is available for use by the general public, or by customers or employees of certain businesses.");

// https://schema.org/Pulmonary
t("Pulmonary");
t("A specific branch of medical science that pertains to the study of the respiratory system and its respective disease states.");

// https://schema.org/QAPage
t("QAPage");
t("A QAPage is a WebPage focussed on a specific Question and its Answer(s), e.g. in a question answering site or documenting Frequently Asked Questions (FAQs).");

// https://schema.org/QualitativeValue
t("QualitativeValue");
t("A predefined value for a product characteristic, e.g. the power cord plug type 'US' or the garment sizes 'S', 'M', 'L', and 'XL'.");

// https://schema.org/QuantitativeValue
t("QuantitativeValue");
t("A point value or interval for product characteristics and other purposes.");

// https://schema.org/QuantitativeValueDistribution
t("QuantitativeValueDistribution");
t("A statistical distribution of values.");

// https://schema.org/Quantity
t("Quantity");
t("Quantities such as distance, time, mass, weight, etc. Particular instances of say Mass are entities like '3 kg' or '4 milligrams'.");

// https://schema.org/Question
t("Question");
t("A specific question - e.g. from a user seeking answers online, or collected in a Frequently Asked Questions (FAQ) document.");

// https://schema.org/Quiz
t("Quiz");
t("Quiz: A test of knowledge, skills and abilities.");

// https://schema.org/Quotation
t("Quotation");
t('A quotation. Often but not necessarily from some written work, attributable to a real world author and - if associated with a fictional character - to any fictional Person. Use <a class="localLink" href="/isBasedOn">isBasedOn</a> to link to source/origin. The <a class="localLink" href="/recordedIn">recordedIn</a> property can be used to reference a Quotation from an <a class="localLink" href="/Event">Event</a>.');
t("A quotation.");

// https://schema.org/QuoteAction
t("QuoteAction");
t("An agent quotes/estimates/appraises an object/product/service with a price at a location/store.");

// https://schema.org/RadiationTherapy
t("RadiationTherapy");
t("A process of care using radiation aimed at improving a health condition.");

// https://schema.org/RadioBroadcastService
t("RadioBroadcastService");
t("A delivery service through which radio content is provided via broadcast over the air or online.");

// https://schema.org/RadioChannel
t("RadioChannel");
t("A unique instance of a radio BroadcastService on a CableOrSatelliteService lineup.");

// https://schema.org/RadioClip
t("RadioClip");
t("A short radio program or a segment/part of a radio program.");

// https://schema.org/RadioEpisode
t("RadioEpisode");
t("A radio episode which can be part of a series or season.");

// https://schema.org/Radiography
t("Radiography");
t("Radiography is an imaging technique that uses electromagnetic radiation other than visible light, especially X-rays, to view the internal structure of a non-uniformly composed and opaque object such as the human body.");

// https://schema.org/RadioSeason
t("RadioSeason");
t("Season dedicated to radio broadcast and associated online delivery.");

// https://schema.org/RadioSeries
t("RadioSeries");
t("CreativeWorkSeries dedicated to radio broadcast and associated online delivery.");

// https://schema.org/RadioStation
t("RadioStation");
t("A radio station.");

// https://schema.org/RandomizedTrial
t("RandomizedTrial");
t("A randomized trial design.");

// https://schema.org/Rating
t("Rating");
t("A rating is an evaluation on a numeric scale, such as 1 to 5 stars.");

// https://schema.org/ReactAction
t("ReactAction");
t("The act of responding instinctively and emotionally to an object, expressing a sentiment.");

// https://schema.org/ReadAction
t("ReadAction");
t("The act of consuming written content.");

// https://schema.org/ReadPermission
t("ReadPermission");
t("Permission to read or view the document.");

// https://schema.org/RealEstateAgent
t("RealEstateAgent");
t("A real-estate agent.");

// https://schema.org/RealEstateListing
t("RealEstateListing");
t('A <a class="localLink" href="/RealEstateListing">RealEstateListing</a> is a listing that describes one or more real-estate <a class="localLink" href="/Offer">Offer</a>s (whose <a class="localLink" href="/businessFunction">businessFunction</a> is typically to lease out, or to sell).
  The <a class="localLink" href="/RealEstateListing">RealEstateListing</a> type itself represents the overall listing, as manifested in some <a class="localLink" href="/WebPage">WebPage</a>.');
t('A <a class="localLink" href="/RealEstateListing">RealEstateListing</a> is a listing that describes one or more real-estate <a class="localLink" href="/Offer">Offer</a>s (whose <a class="localLink" href="/businessFunction">businessFunction</a> is typically to lease out, or to sell).');

// https://schema.org/RearWheelDriveConfiguration
t("RearWheelDriveConfiguration");
t("Real-wheel drive is a transmission layout where the engine drives the rear wheels.");

// https://schema.org/ReceiveAction
t("ReceiveAction");
t('The act of physically/electronically taking delivery of an object that has been transferred from an origin to a destination. Reciprocal of SendAction.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/SendAction">SendAction</a>: The reciprocal of ReceiveAction.</li>
<li><a class="localLink" href="/TakeAction">TakeAction</a>: Unlike TakeAction, ReceiveAction does not imply that the ownership has been transferred (e.g. I can receive a package, but it does not mean the package is now mine).</li>
</ul>');
t("The act of physically/electronically taking delivery of an object that has been transferred from an origin to a destination.");

// https://schema.org/Recipe
t("Recipe");
t('A recipe. For dietary restrictions covered by the recipe, a few common restrictions are enumerated via <a class="localLink" href="/suitableForDiet">suitableForDiet</a>. The <a class="localLink" href="/keywords">keywords</a> property can also be used to add more detail.');
t("A recipe.");

// https://schema.org/Recommendation
t("Recommendation");
t('<a class="localLink" href="/Recommendation">Recommendation</a> is a type of <a class="localLink" href="/Review">Review</a> that suggests or proposes something as the best option or best course of action. Recommendations may be for products or services, or other concrete things, as in the case of a ranked list or product guide. A <a class="localLink" href="/Guide">Guide</a> may list multiple recommendations for different categories. For example, in a <a class="localLink" href="/Guide">Guide</a> about which TVs to buy, the author may have several <a class="localLink" href="/Recommendation">Recommendation</a>s.');
t('<a class="localLink" href="/Recommendation">Recommendation</a> is a type of <a class="localLink" href="/Review">Review</a> that suggests or proposes something as the best option or best course of action.');

// https://schema.org/RecommendedDoseSchedule
t("RecommendedDoseSchedule");
t("A recommended dosing schedule for a drug or supplement as prescribed or recommended by an authority or by the drug/supplement's manufacturer. Capture the recommending authority in the recognizingAuthority property of MedicalEntity.");
t("A recommended dosing schedule for a drug or supplement as prescribed or recommended by an authority or by the drug/supplement's manufacturer.");

// https://schema.org/Recruiting
t("Recruiting");
t("Recruiting participants.");

// https://schema.org/RecyclingCenter
t("RecyclingCenter");
t("A recycling center.");

// https://schema.org/ReducedRelevanceForChildrenConsideration
t("ReducedRelevanceForChildrenConsideration");
t("A general code for cases where relevance to children is reduced, e.g. adult education, mortgages, retirement-related products, etc.");

// https://schema.org/RefundTypeEnumeration
t("RefundTypeEnumeration");
t("Enumerates several kinds of product return refund types.");

// https://schema.org/RefurbishedCondition
t("RefurbishedCondition");
t("Indicates that the item is refurbished.");

// https://schema.org/RegisterAction
t("RegisterAction");
t('The act of registering to be a user of a service, product or web page.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/JoinAction">JoinAction</a>: Unlike JoinAction, RegisterAction implies you are registering to be a user of a service, <em>not</em> a group/team of people.</li>
<li><a class="localLink" href="/FollowAction">FollowAction</a>: Unlike FollowAction, RegisterAction doesn\'t imply that the agent is expecting to poll for updates from the object.</li>
<li><a class="localLink" href="/SubscribeAction">SubscribeAction</a>: Unlike SubscribeAction, RegisterAction doesn\'t imply that the agent is expecting updates from the object.</li>
</ul>');
t("The act of registering to be a user of a service, product or web page.");

// https://schema.org/Registry
t("Registry");
t("A registry-based study design.");

// https://schema.org/ReimbursementCap
t("ReimbursementCap");
t("The drug's cost represents the maximum reimbursement paid by an insurer for the drug.");

// https://schema.org/RejectAction
t("RejectAction");
t('The act of rejecting to/adopting an object.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/AcceptAction">AcceptAction</a>: The antonym of RejectAction.</li>
</ul>');
t("The act of rejecting to/adopting an object.");

// https://schema.org/RelatedTopicsHealthAspect
t("RelatedTopicsHealthAspect");
t("Other prominent or relevant topics tied to the main topic.");

// https://schema.org/RemixAlbum
t("RemixAlbum");
t("RemixAlbum.");

// https://schema.org/Renal
t("Renal");
t("A specific branch of medical science that pertains to the study of the kidneys and its respective disease states.");

// https://schema.org/RentAction
t("RentAction");
t("The act of giving money in return for temporary use, but not ownership, of an object such as a vehicle or property. For example, an agent rents a property from a landlord in exchange for a periodic payment.");
t("The act of giving money in return for temporary use, but not ownership, of an object such as a vehicle or property.");

// https://schema.org/RentalCarReservation
t("RentalCarReservation");
t("A reservation for a rental car.<br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations.");
t("A reservation for a rental car.");

// https://schema.org/RentalVehicleUsage
t("RentalVehicleUsage");
t("Indicates the usage of the vehicle as a rental car.");

// https://schema.org/RepaymentSpecification
t("RepaymentSpecification");
t("A structured value representing repayment.");

// https://schema.org/ReplaceAction
t("ReplaceAction");
t("The act of editing a recipient by replacing an old object with a new object.");

// https://schema.org/ReplyAction
t("ReplyAction");
t('The act of responding to a question/message asked/sent by the object. Related to <a class="localLink" href="/AskAction">AskAction</a>.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/AskAction">AskAction</a>: Appears generally as an origin of a ReplyAction.</li>
</ul>');
t("The act of responding to a question/message asked/sent by the object.");

// https://schema.org/Report
t("Report");
t("A Report generated by governmental or non-governmental organization.");

// https://schema.org/ReportageNewsArticle
t("ReportageNewsArticle");
t('The <a class="localLink" href="/ReportageNewsArticle">ReportageNewsArticle</a> type is a subtype of <a class="localLink" href="/NewsArticle">NewsArticle</a> representing
 news articles which are the result of journalistic news reporting conventions.<br/><br/>

In practice many news publishers produce a wide variety of article types, many of which might be considered a <a class="localLink" href="/NewsArticle">NewsArticle</a> but not a <a class="localLink" href="/ReportageNewsArticle">ReportageNewsArticle</a>. For example, opinion pieces, reviews, analysis, sponsored or satirical articles, or articles that combine several of these elements.<br/><br/>

The <a class="localLink" href="/ReportageNewsArticle">ReportageNewsArticle</a> type is based on a stricter ideal for "news" as a work of journalism, with articles based on factual information either observed or verified by the author, or reported and verified from knowledgeable sources.  This often includes perspectives from multiple viewpoints on a particular issue (distinguishing news reports from public relations or propaganda).  News reports in the <a class="localLink" href="/ReportageNewsArticle">ReportageNewsArticle</a> sense de-emphasize the opinion of the author, with commentary and value judgements typically expressed elsewhere.<br/><br/>

A <a class="localLink" href="/ReportageNewsArticle">ReportageNewsArticle</a> which goes deeper into analysis can also be marked with an additional type of <a class="localLink" href="/AnalysisNewsArticle">AnalysisNewsArticle</a>.');
t('The <a class="localLink" href="/ReportageNewsArticle">ReportageNewsArticle</a> type is a subtype of <a class="localLink" href="/NewsArticle">NewsArticle</a> representing
 news articles which are the result of journalistic news reporting conventions.');

// https://schema.org/ReportedDoseSchedule
t("ReportedDoseSchedule");
t("A patient-reported or observed dosing schedule for a drug or supplement.");

// https://schema.org/Researcher
t("Researcher");
t("Researchers.");

// https://schema.org/ResearchOrganization
t("ResearchOrganization");
t("A Research Organization (e.g. scientific institute, research company).");

// https://schema.org/ResearchProject
t("ResearchProject");
t("A Research project.");

// https://schema.org/Reservation
t("Reservation");
t('Describes a reservation for travel, dining or an event. Some reservations require tickets. <br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations. For offers of tickets, restaurant reservations, flights, or rental cars, use <a class="localLink" href="/Offer">Offer</a>.');
t("Describes a reservation for travel, dining or an event.");

// https://schema.org/ReservationCancelled
t("ReservationCancelled");
t("The status for a previously confirmed reservation that is now cancelled.");

// https://schema.org/ReservationConfirmed
t("ReservationConfirmed");
t("The status of a confirmed reservation.");

// https://schema.org/ReservationHold
t("ReservationHold");
t("The status of a reservation on hold pending an update like credit card number or flight changes.");

// https://schema.org/ReservationPackage
t("ReservationPackage");
t("A group of multiple reservations with common values for all sub-reservations.");

// https://schema.org/ReservationPending
t("ReservationPending");
t("The status of a reservation when a request has been sent, but not confirmed.");

// https://schema.org/ReservationStatusType
t("ReservationStatusType");
t("Enumerated status values for Reservation.");

// https://schema.org/ReserveAction
t("ReserveAction");
t('Reserving a concrete object.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/ScheduleAction">ScheduleAction</a>: Unlike ScheduleAction, ReserveAction reserves concrete objects (e.g. a table, a hotel) towards a time slot / spatial allocation.</li>
</ul>');
t("Reserving a concrete object.");

// https://schema.org/Reservoir
t("Reservoir");
t("A reservoir of water, typically an artificially created lake, like the Lake Kariba reservoir.");

// https://schema.org/Residence
t("Residence");
t("The place where a person lives.");

// https://schema.org/Resort
t("Resort");
t('A resort is a place used for relaxation or recreation, attracting visitors for holidays or vacations. Resorts are places, towns or sometimes commercial establishments operated by a single company (source: Wikipedia, the free encyclopedia, see <a href="http://en.wikipedia.org/wiki/Resort">http://en.wikipedia.org/wiki/Resort</a>).
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("A resort is a place used for relaxation or recreation, attracting visitors for holidays or vacations.");

// https://schema.org/RespiratoryTherapy
t("RespiratoryTherapy");
t("The therapy that is concerned with the maintenance or improvement of respiratory function (as in patients with pulmonary disease).");

// https://schema.org/Restaurant
t("Restaurant");
t("A restaurant.");

// https://schema.org/RestockingFees
t("RestockingFees");
t("Specifies that the customer must pay a restocking fee when returning a product.");

// https://schema.org/RestrictedDiet
t("RestrictedDiet");
t("A diet restricted to certain foods or preparations for cultural, religious, health or lifestyle reasons.");

// https://schema.org/ResultsAvailable
t("ResultsAvailable");
t("Results are available.");

// https://schema.org/ResultsNotAvailable
t("ResultsNotAvailable");
t("Results are not available.");

// https://schema.org/ResumeAction
t("ResumeAction");
t("The act of resuming a device or application which was formerly paused (e.g. resume music playback or resume a timer).");

// https://schema.org/Retail
t("Retail");
t("The drug's cost represents the retail cost of the drug.");

// https://schema.org/ReturnAction
t("ReturnAction");
t("The act of returning to the origin that which was previously received (concrete objects) or taken (ownership).");

// https://schema.org/ReturnAtKiosk
t("ReturnAtKiosk");
t("Specifies that product returns must be made at a kiosk.");

// https://schema.org/ReturnByMail
t("ReturnByMail");
t("Specifies that product returns must be done by mail.");

// https://schema.org/ReturnFeesCustomerResponsibility
t("ReturnFeesCustomerResponsibility");
t("Specifies that product returns must be paid for, and are the responsibility of, the customer.");

// https://schema.org/ReturnFeesEnumeration
t("ReturnFeesEnumeration");
t("Enumerates several kinds of policies for product return fees.");

// https://schema.org/ReturnInStore
t("ReturnInStore");
t("Specifies that product returns must be made in a store.");

// https://schema.org/ReturnLabelCustomerResponsibility
t("ReturnLabelCustomerResponsibility");
t("Indicated that creating a return label is the responsibility of the customer.");

// https://schema.org/ReturnLabelDownloadAndPrint
t("ReturnLabelDownloadAndPrint");
t("Indicated that a return label must be downloaded and printed by the customer.");

// https://schema.org/ReturnLabelInBox
t("ReturnLabelInBox");
t("Specifies that a return label will be provided by the seller in the shipping box.");

// https://schema.org/ReturnLabelSourceEnumeration
t("ReturnLabelSourceEnumeration");
t("Enumerates several types of return labels for product returns.");

// https://schema.org/ReturnMethodEnumeration
t("ReturnMethodEnumeration");
t("Enumerates several types of product return methods.");

// https://schema.org/ReturnShippingFees
t("ReturnShippingFees");
t("Specifies that the customer must pay the return shipping costs when returning a product.");

// https://schema.org/Review
t("Review");
t("A review of an item - for example, of a restaurant, movie, or store.");

// https://schema.org/ReviewAction
t("ReviewAction");
t("The act of producing a balanced opinion about the object for an audience. An agent reviews an object with participants resulting in a review.");
t("The act of producing a balanced opinion about the object for an audience.");

// https://schema.org/ReviewNewsArticle
t("ReviewNewsArticle");
t('A <a class="localLink" href="/NewsArticle">NewsArticle</a> and <a class="localLink" href="/CriticReview">CriticReview</a> providing a professional critic\'s assessment of a service, product, performance, or artistic or literary work.');

// https://schema.org/Rheumatologic
t("Rheumatologic");
t("A specific branch of medical science that deals with the study and treatment of rheumatic, autoimmune or joint diseases.");

// https://schema.org/RightHandDriving
t("RightHandDriving");
t("The steering position is on the right side of the vehicle (viewed from the main direction of driving).");

// https://schema.org/RisksOrComplicationsHealthAspect
t("RisksOrComplicationsHealthAspect");
t("Information about the risk factors and possible complications that may follow a topic.");

// https://schema.org/RiverBodyOfWater
t("RiverBodyOfWater");
t("A river (for example, the broad majestic Shannon).");

// https://schema.org/Role
t("Role");
t('Represents additional information about a relationship or property. For example a Role can be used to say that a \'member\' role linking some SportsTeam to a player occurred during a particular time period. Or that a Person\'s \'actor\' role in a Movie was for some particular characterName. Such properties can be attached to a Role entity, which is then associated with the main entities using ordinary properties like \'member\' or \'actor\'.<br/><br/>

See also <a href="http://blog.schema.org/2014/06/introducing-role.html">blog post</a>.');
t("Represents additional information about a relationship or property.");

// https://schema.org/RoofingContractor
t("RoofingContractor");
t("A roofing contractor.");

// https://schema.org/Room
t("Room");
t('A room is a distinguishable space within a structure, usually separated from other spaces by interior walls (source: Wikipedia, the free encyclopedia, see <a href="http://en.wikipedia.org/wiki/Room">http://en.wikipedia.org/wiki/Room</a>).
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t("A room is a distinguishable space within a structure, usually separated from other spaces by interior walls.");

// https://schema.org/RsvpAction
t("RsvpAction");
t("The act of notifying an event organizer as to whether you expect to attend the event.");

// https://schema.org/RsvpResponseMaybe
t("RsvpResponseMaybe");
t("The invitee may or may not attend.");

// https://schema.org/RsvpResponseNo
t("RsvpResponseNo");
t("The invitee will not attend.");

// https://schema.org/RsvpResponseType
t("RsvpResponseType");
t("RsvpResponseType is an enumeration type whose instances represent responding to an RSVP request.");

// https://schema.org/RsvpResponseYes
t("RsvpResponseYes");
t("The invitee will attend.");

// https://schema.org/RVPark
t("RVPark");
t('A place offering space for "Recreational Vehicles", Caravans, mobile homes and the like.');

// https://schema.org/SafetyHealthAspect
t("SafetyHealthAspect");
t("Content about the safety-related aspects of a health topic.");

// https://schema.org/SaleEvent
t("SaleEvent");
t("Event type: Sales event.");

// https://schema.org/SalePrice
t("SalePrice");
t("Represents a sale price (usually active for a limited period) of an offered product.");

// https://schema.org/SatireOrParodyContent
t("SatireOrParodyContent");
t('Content coded \'satire or parody content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.<br/><br/>

For a <a class="localLink" href="/VideoObject">VideoObject</a> to be \'satire or parody content\': A video that was created as political or humorous commentary and is presented in that context. (Reshares of satire/parody content that do not include relevant context are more likely to fall under the “missing context” rating.)<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> to be \'satire or parody content\': An image that was created as political or humorous commentary and is presented in that context. (Reshares of satire/parody content that do not include relevant context are more likely to fall under the “missing context” rating.)<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> with embedded text to be \'satire or parody content\': An image that was created as political or humorous commentary and is presented in that context. (Reshares of satire/parody content that do not include relevant context are more likely to fall under the “missing context” rating.)<br/><br/>

For an <a class="localLink" href="/AudioObject">AudioObject</a> to be \'satire or parody content\': Audio that was created as political or humorous commentary and is presented in that context. (Reshares of satire/parody content that do not include relevant context are more likely to fall under the “missing context” rating.)');
t('Content coded \'satire or parody content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.');

// https://schema.org/SatiricalArticle
t("SatiricalArticle");
t('An <a class="localLink" href="/Article">Article</a> whose content is primarily <a href="https://en.wikipedia.org/wiki/Satire">[satirical]</a> in nature, i.e. unlikely to be literally true. A satirical article is sometimes but not necessarily also a <a class="localLink" href="/NewsArticle">NewsArticle</a>. <a class="localLink" href="/ScholarlyArticle">ScholarlyArticle</a>s are also sometimes satirized.');
t('An <a class="localLink" href="/Article">Article</a> whose content is primarily <a href="https://en.wikipedia.org/wiki/Satire">[satirical]</a> in nature, i.e. unlikely to be literally true.');

// https://schema.org/Saturday
t("Saturday");
t("The day of the week between Friday and Sunday.");

// https://schema.org/Schedule
t("Schedule");
t('A schedule defines a repeating time period used to describe a regularly occurring <a class="localLink" href="/Event">Event</a>. At a minimum a schedule will specify <a class="localLink" href="/repeatFrequency">repeatFrequency</a> which describes the interval between occurrences of the event. Additional information can be provided to specify the schedule more precisely.
      This includes identifying the day(s) of the week or month when the recurring event will take place, in addition to its start and end time. Schedules may also
      have start and end dates to indicate when they are active, e.g. to define a limited calendar of events.');
t('A schedule defines a repeating time period used to describe a regularly occurring <a class="localLink" href="/Event">Event</a>.');

// https://schema.org/ScheduleAction
t("ScheduleAction");
t('Scheduling future actions, events, or tasks.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/ReserveAction">ReserveAction</a>: Unlike ReserveAction, ScheduleAction allocates future actions (e.g. an event, a task, etc) towards a time slot / spatial allocation.</li>
</ul>');
t("Scheduling future actions, events, or tasks.");

// https://schema.org/ScholarlyArticle
t("ScholarlyArticle");
t("A scholarly article.");

// https://schema.org/School
t("School");
t("A school.");

// https://schema.org/SchoolDistrict
t("SchoolDistrict");
t("A School District is an administrative area for the administration of schools.");

// https://schema.org/ScreeningEvent
t("ScreeningEvent");
t("A screening of a movie or other video.");

// https://schema.org/ScreeningHealthAspect
t("ScreeningHealthAspect");
t("Content about how to screen or further filter a topic.");

// https://schema.org/Sculpture
t("Sculpture");
t("A piece of sculpture.");

// https://schema.org/SeaBodyOfWater
t("SeaBodyOfWater");
t("A sea (for example, the Caspian sea).");

// https://schema.org/SearchAction
t("SearchAction");
t('The act of searching for an object.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/FindAction">FindAction</a>: SearchAction generally leads to a FindAction, but not necessarily.</li>
</ul>');
t("The act of searching for an object.");

// https://schema.org/SearchRescueOrganization
t("SearchRescueOrganization");
t("A Search and Rescue organization of some kind.");

// https://schema.org/SearchResultsPage
t("SearchResultsPage");
t("Web page type: Search results page.");

// https://schema.org/Season
t("Season");
t("A media season, e.g. TV, radio, video game etc.");

// https://schema.org/Seat
t("Seat");
t("Used to describe a seat, such as a reserved seat in an event reservation.");

// https://schema.org/SeatingMap
t("SeatingMap");
t("A seating map.");

// https://schema.org/SeeDoctorHealthAspect
t("SeeDoctorHealthAspect");
t("Information about questions that may be asked, when to see a professional, measures before seeing a doctor or content about the first consultation.");

// https://schema.org/SeekToAction
t("SeekToAction");
t('This is the <a class="localLink" href="/Action">Action</a> of navigating to a specific <a class="localLink" href="/startOffset">startOffset</a> timestamp within a <a class="localLink" href="/VideoObject">VideoObject</a>, typically represented with a URL template structure.');

// https://schema.org/SelfCareHealthAspect
t("SelfCareHealthAspect");
t("Self care actions or measures that can be taken to sooth, health or avoid a topic. This may be carried at home and can be carried/managed by the person itself.");
t("Self care actions or measures that can be taken to sooth, health or avoid a topic.");

// https://schema.org/SelfStorage
t("SelfStorage");
t("A self-storage facility.");

// https://schema.org/SellAction
t("SellAction");
t("The act of taking money from a buyer in exchange for goods or services rendered. An agent sells an object, product, or service to a buyer for a price. Reciprocal of BuyAction.");
t("The act of taking money from a buyer in exchange for goods or services rendered.");

// https://schema.org/SendAction
t("SendAction");
t('The act of physically/electronically dispatching an object for transfer from an origin to a destination. Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/ReceiveAction">ReceiveAction</a>: The reciprocal of SendAction.</li>
<li><a class="localLink" href="/GiveAction">GiveAction</a>: Unlike GiveAction, SendAction does not imply the transfer of ownership (e.g. I can send you my laptop, but I\'m not necessarily giving it to you).</li>
</ul>');
t("The act of physically/electronically dispatching an object for transfer from an origin to a destination.");

// https://schema.org/Series
t("Series");
t('A Series in schema.org is a group of related items, typically but not necessarily of the same kind. See also <a class="localLink" href="/CreativeWorkSeries">CreativeWorkSeries</a>, <a class="localLink" href="/EventSeries">EventSeries</a>.');
t("A Series in schema.org is a group of related items, typically but not necessarily of the same kind.");

// https://schema.org/Service
t("Service");
t("A service provided by an organization, e.g. delivery service, print services, etc.");

// https://schema.org/ServiceChannel
t("ServiceChannel");
t("A means for accessing a service, e.g. a government office location, web site, or phone number.");

// https://schema.org/SexualContentConsideration
t("SexualContentConsideration");
t("The item contains sexually oriented content such as nudity, suggestive or explicit material, or related online services, or is intended to enhance sexual activity. Examples: Erotic videos or magazine, sexual enhancement devices, sex toys.");
t("The item contains sexually oriented content such as nudity, suggestive or explicit material, or related online services, or is intended to enhance sexual activity.");

// https://schema.org/ShareAction
t("ShareAction");
t("The act of distributing content to people for their amusement or edification.");

// https://schema.org/SheetMusic
t("SheetMusic");
t("Printed music, as opposed to performed or recorded music.");

// https://schema.org/ShippingDeliveryTime
t("ShippingDeliveryTime");
t("ShippingDeliveryTime provides various pieces of information about delivery times for shipping.");

// https://schema.org/ShippingRateSettings
t("ShippingRateSettings");
t('A ShippingRateSettings represents re-usable pieces of shipping information. It is designed for publication on an URL that may be referenced via the <a class="localLink" href="/shippingSettingsLink">shippingSettingsLink</a> property of an <a class="localLink" href="/OfferShippingDetails">OfferShippingDetails</a>. Several occurrences can be published, distinguished and matched (i.e. identified/referenced) by their different values for <a class="localLink" href="/shippingLabel">shippingLabel</a>.');
t("A ShippingRateSettings represents re-usable pieces of shipping information.");

// https://schema.org/ShoeStore
t("ShoeStore");
t("A shoe store.");

// https://schema.org/ShoppingCenter
t("ShoppingCenter");
t("A shopping center or mall.");

// https://schema.org/ShortStory
t("ShortStory");
t("Short story or tale. A brief work of literature, usually written in narrative prose.");
t("Short story or tale.");

// https://schema.org/SideEffectsHealthAspect
t("SideEffectsHealthAspect");
t("Side effects that can be observed from the usage of the topic.");

// https://schema.org/SingleBlindedTrial
t("SingleBlindedTrial");
t("A trial design in which the researcher knows which treatment the patient was randomly assigned to but the patient does not.");

// https://schema.org/SingleCenterTrial
t("SingleCenterTrial");
t("A trial that takes place at a single center.");

// https://schema.org/SingleFamilyResidence
t("SingleFamilyResidence");
t("Residence type: Single-family home.");

// https://schema.org/SinglePlayer
t("SinglePlayer");
t("Play mode: SinglePlayer. Which is played by a lone player.");
t("Play mode: SinglePlayer.");

// https://schema.org/SingleRelease
t("SingleRelease");
t("SingleRelease.");

// https://schema.org/SiteNavigationElement
t("SiteNavigationElement");
t("A navigation element of the page.");

// https://schema.org/SizeGroupEnumeration
t("SizeGroupEnumeration");
t("Enumerates common size groups for various product categories.");

// https://schema.org/SizeSpecification
t("SizeSpecification");
t('Size related properties of a product, typically a size code (<a class="localLink" href="/name">name</a>) and optionally a <a class="localLink" href="/sizeSystem">sizeSystem</a>, <a class="localLink" href="/sizeGroup">sizeGroup</a>, and product measurements (<a class="localLink" href="/hasMeasurement">hasMeasurement</a>). In addition, the intended audience can be defined through <a class="localLink" href="/suggestedAge">suggestedAge</a>, <a class="localLink" href="/suggestedGender">suggestedGender</a>, and suggested body measurements (<a class="localLink" href="/suggestedMeasurement">suggestedMeasurement</a>).');
t('Size related properties of a product, typically a size code (<a class="localLink" href="/name">name</a>) and optionally a <a class="localLink" href="/sizeSystem">sizeSystem</a>, <a class="localLink" href="/sizeGroup">sizeGroup</a>, and product measurements (<a class="localLink" href="/hasMeasurement">hasMeasurement</a>).');

// https://schema.org/SizeSystemEnumeration
t("SizeSystemEnumeration");
t('Enumerates common size systems for different categories of products, for example "EN-13402" or "UK" for wearables or "Imperial" for screws.');

// https://schema.org/SizeSystemImperial
t("SizeSystemImperial");
t("Imperial size system.");

// https://schema.org/SizeSystemMetric
t("SizeSystemMetric");
t("Metric size system.");

// https://schema.org/Skin
t("Skin");
t("Skin assessment with clinical examination.");

// https://schema.org/SkiResort
t("SkiResort");
t("A ski resort.");

// https://schema.org/SocialEvent
t("SocialEvent");
t("Event type: Social event.");

// https://schema.org/SocialMediaPosting
t("SocialMediaPosting");
t("A post to a social media platform, including blog posts, tweets, Facebook posts, etc.");

// https://schema.org/SoftwareApplication
t("SoftwareApplication");
t("A software application.");

// https://schema.org/SoftwareSourceCode
t("SoftwareSourceCode");
t("Computer programming source code. Example: Full (compile ready) solutions, code snippet samples, scripts, templates.");
t("Computer programming source code.");

// https://schema.org/SoldOut
t("SoldOut");
t("Indicates that the item has sold out.");

// https://schema.org/SolveMathAction
t("SolveMathAction");
t("The action that takes in a math expression and directs users to a page potentially capable of solving/simplifying that expression.");

// https://schema.org/SomeProducts
t("SomeProducts");
t("A placeholder for multiple similar products of the same kind.");

// https://schema.org/SoundtrackAlbum
t("SoundtrackAlbum");
t("SoundtrackAlbum.");

// https://schema.org/SpeakableSpecification
t("SpeakableSpecification");
t('A SpeakableSpecification indicates (typically via <a class="localLink" href="/xpath">xpath</a> or <a class="localLink" href="/cssSelector">cssSelector</a>) sections of a document that are highlighted as particularly <a class="localLink" href="/speakable">speakable</a>. Instances of this type are expected to be used primarily as values of the <a class="localLink" href="/speakable">speakable</a> property.');
t('A SpeakableSpecification indicates (typically via <a class="localLink" href="/xpath">xpath</a> or <a class="localLink" href="/cssSelector">cssSelector</a>) sections of a document that are highlighted as particularly <a class="localLink" href="/speakable">speakable</a>.');

// https://schema.org/SpecialAnnouncement
t("SpecialAnnouncement");
t('A SpecialAnnouncement combines a simple date-stamped textual information update
      with contextualized Web links and other structured data.  It represents an information update made by a
      locally-oriented organization, for example schools, pharmacies, healthcare providers,  community groups, police,
      local government.<br/><br/>

For work in progress guidelines on Coronavirus-related markup see <a href="https://docs.google.com/document/d/14ikaGCKxo50rRM7nvKSlbUpjyIk2WMQd3IkB1lItlrM/edit#">this doc</a>.<br/><br/>

The motivating scenario for SpecialAnnouncement is the <a href="https://en.wikipedia.org/wiki/2019%E2%80%9320_coronavirus_pandemic">Coronavirus pandemic</a>, and the initial vocabulary is oriented to this urgent situation. Schema.org
expect to improve the markup iteratively as it is deployed and as feedback emerges from use. In addition to our
usual <a href="https://github.com/schemaorg/schemaorg/issues/2490">Github entry</a>, feedback comments can also be provided in <a href="https://docs.google.com/document/d/1fpdFFxk8s87CWwACs53SGkYv3aafSxz_DTtOQxMrBJQ/edit#">this document</a>.<br/><br/>

While this schema is designed to communicate urgent crisis-related information, it is not the same as an emergency warning technology like <a href="https://en.wikipedia.org/wiki/Common_Alerting_Protocol">CAP</a>, although there may be overlaps. The intent is to cover
the kinds of everyday practical information being posted to existing websites during an emergency situation.<br/><br/>

Several kinds of information can be provided:<br/><br/>

We encourage the provision of "name", "text", "datePosted", "expires" (if appropriate), "category" and
"url" as a simple baseline. It is important to provide a value for "category" where possible, most ideally as a well known
URL from Wikipedia or Wikidata. In the case of the 2019-2020 Coronavirus pandemic, this should be "https://en.wikipedia.org/w/index.php?title=2019-20_coronavirus_pandemic" or "https://www.wikidata.org/wiki/Q81068910".<br/><br/>

For many of the possible properties, values can either be simple links or an inline description, depending on whether a summary is available. For a link, provide just the URL of the appropriate page as the property\'s value. For an inline description, use a <a class="localLink" href="/WebContent">WebContent</a> type, and provide the url as a property of that, alongside at least a simple "<a class="localLink" href="/text">text</a>" summary of the page. It is
unlikely that a single SpecialAnnouncement will need all of the possible properties simultaneously.<br/><br/>

We expect that in many cases the page referenced might contain more specialized structured data, e.g. contact info, <a class="localLink" href="/openingHours">openingHours</a>, <a class="localLink" href="/Event">Event</a>, <a class="localLink" href="/FAQPage">FAQPage</a> etc. By linking to those pages from a <a class="localLink" href="/SpecialAnnouncement">SpecialAnnouncement</a> you can help make it clearer that the events are related to the situation (e.g. Coronavirus) indicated by the <a class="localLink" href="/category">category</a> property of the <a class="localLink" href="/SpecialAnnouncement">SpecialAnnouncement</a>.<br/><br/>

Many <a class="localLink" href="/SpecialAnnouncement">SpecialAnnouncement</a>s will relate to particular regions and to identifiable local organizations. Use <a class="localLink" href="/spatialCoverage">spatialCoverage</a> for the region, and <a class="localLink" href="/announcementLocation">announcementLocation</a> to indicate specific <a class="localLink" href="/LocalBusiness">LocalBusiness</a>es and <a class="localLink" href="/CivicStructure">CivicStructure</a>s. If the announcement affects both a particular region and a specific location (for example, a library closure that serves an entire region), use both <a class="localLink" href="/spatialCoverage">spatialCoverage</a> and <a class="localLink" href="/announcementLocation">announcementLocation</a>.<br/><br/>

The <a class="localLink" href="/about">about</a> property can be used to indicate entities that are the focus of the announcement. We now recommend using <a class="localLink" href="/about">about</a> only
for representing non-location entities (e.g. a <a class="localLink" href="/Course">Course</a> or a <a class="localLink" href="/RadioStation">RadioStation</a>). For places, use <a class="localLink" href="/announcementLocation">announcementLocation</a> and <a class="localLink" href="/spatialCoverage">spatialCoverage</a>. Consumers of this markup should be aware that the initial design encouraged the use of <a class="localLink" href="/about">about</a> for locations too.<br/><br/>

The basic content of <a class="localLink" href="/SpecialAnnouncement">SpecialAnnouncement</a> is similar to that of an <a href="https://en.wikipedia.org/wiki/RSS">RSS</a> or <a href="https://en.wikipedia.org/wiki/Atom_(Web_standard)">Atom</a> feed. For publishers without such feeds, basic feed-like information can be shared by posting
<a class="localLink" href="/SpecialAnnouncement">SpecialAnnouncement</a> updates in a page, e.g. using JSON-LD. For sites with Atom/RSS functionality, you can point to a feed
with the <a class="localLink" href="/webFeed">webFeed</a> property. This can be a simple URL, or an inline <a class="localLink" href="/DataFeed">DataFeed</a> object, with <a class="localLink" href="/encodingFormat">encodingFormat</a> providing
media type information, e.g. "application/rss+xml" or "application/atom+xml".');
t("A SpecialAnnouncement combines a simple date-stamped textual information update
      with contextualized Web links and other structured data.");

// https://schema.org/Specialty
t("Specialty");
t("Any branch of a field in which people typically develop specific expertise, usually after significant study, time, and effort.");

// https://schema.org/SpeechPathology
t("SpeechPathology");
t("The scientific study and treatment of defects, disorders, and malfunctions of speech and voice, as stuttering, lisping, or lalling, and of language disturbances, as aphasia or delayed language acquisition.");

// https://schema.org/SpokenWordAlbum
t("SpokenWordAlbum");
t("SpokenWordAlbum.");

// https://schema.org/SportingGoodsStore
t("SportingGoodsStore");
t("A sporting goods store.");

// https://schema.org/SportsActivityLocation
t("SportsActivityLocation");
t("A sports location, such as a playing field.");

// https://schema.org/SportsClub
t("SportsClub");
t("A sports club.");

// https://schema.org/SportsEvent
t("SportsEvent");
t("Event type: Sports event.");

// https://schema.org/SportsOrganization
t("SportsOrganization");
t("Represents the collection of all sports organizations, including sports teams, governing bodies, and sports associations.");

// https://schema.org/SportsTeam
t("SportsTeam");
t("Organization: Sports team.");

// https://schema.org/SpreadsheetDigitalDocument
t("SpreadsheetDigitalDocument");
t("A spreadsheet file.");

// https://schema.org/SRP
t("SRP");
t('Represents the suggested retail price ("SRP") of an offered product.');

// https://schema.org/StadiumOrArena
t("StadiumOrArena");
t("A stadium.");

// https://schema.org/StagedContent
t("StagedContent");
t('Content coded \'staged content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.<br/><br/>

For a <a class="localLink" href="/VideoObject">VideoObject</a> to be \'staged content\': A video that has been created using actors or similarly contrived.<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> to be \'staged content\': An image that was created using actors or similarly contrived, such as a screenshot of a fake tweet.<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> with embedded text to be \'staged content\': An image that was created using actors or similarly contrived, such as a screenshot of a fake tweet.<br/><br/>

For an <a class="localLink" href="/AudioObject">AudioObject</a> to be \'staged content\': Audio that has been created using actors or similarly contrived.');
t('Content coded \'staged content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.');

// https://schema.org/StagesHealthAspect
t("StagesHealthAspect");
t("Stages that can be observed from a topic.");

// https://schema.org/State
t("State");
t("A state or province of a country.");

// https://schema.org/Statement
t("Statement");
t('A statement about something, for example a fun or interesting fact. If known, the main entity this statement is about can be indicated using mainEntity. For more formal claims (e.g. in Fact Checking), consider using <a class="localLink" href="/Claim">Claim</a> instead. Use the <a class="localLink" href="/text">text</a> property to capture the text of the statement.');
t("A statement about something, for example a fun or interesting fact.");

// https://schema.org/StatisticalPopulation
t("StatisticalPopulation");
t('A StatisticalPopulation is a set of instances of a certain given type that satisfy some set of constraints. The property <a class="localLink" href="/populationType">populationType</a> is used to specify the type. Any property that can be used on instances of that type can appear on the statistical population. For example, a <a class="localLink" href="/StatisticalPopulation">StatisticalPopulation</a> representing all <a class="localLink" href="/Person">Person</a>s with a <a class="localLink" href="/homeLocation">homeLocation</a> of East Podunk California would be described by applying the appropriate <a class="localLink" href="/homeLocation">homeLocation</a> and <a class="localLink" href="/populationType">populationType</a> properties to a <a class="localLink" href="/StatisticalPopulation">StatisticalPopulation</a> item that stands for that set of people.
The properties <a class="localLink" href="/numConstraints">numConstraints</a> and <a class="localLink" href="/constraintProperty">constraintProperty</a> are used to specify which of the populations properties are used to specify the population. Note that the sense of "population" used here is the general sense of a statistical
population, and does not imply that the population consists of people. For example, a <a class="localLink" href="/populationType">populationType</a> of <a class="localLink" href="/Event">Event</a> or <a class="localLink" href="/NewsArticle">NewsArticle</a> could be used. See also <a class="localLink" href="/Observation">Observation</a>, where a <a class="localLink" href="/populationType">populationType</a> such as <a class="localLink" href="/Person">Person</a> or <a class="localLink" href="/Event">Event</a> can be indicated directly. In most cases it may be better to use <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> instead of <a class="localLink" href="/StatisticalPopulation">StatisticalPopulation</a>.');
t("A StatisticalPopulation is a set of instances of a certain given type that satisfy some set of constraints.");

// https://schema.org/StatisticalVariable
t("StatisticalVariable");
t('<a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> represents any type of statistical metric that can be measured at a place and time. The usage pattern for <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> is typically expressed using <a class="localLink" href="/Observation">Observation</a> with an explicit <a class="localLink" href="/populationType">populationType</a>, which is a type, typically drawn from Schema.org. Each <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> is marked as a <a class="localLink" href="/ConstraintNode">ConstraintNode</a>, meaning that some properties (those listed using <a class="localLink" href="/constraintProperty">constraintProperty</a>) serve in this setting solely to define the statistical variable rather than literally describe a specific person, place or thing. For example, a <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> Median<em>Height</em>Person_Female representing the median height of women, could be written as follows: the population type is <a class="localLink" href="/Person">Person</a>; the measuredProperty <a class="localLink" href="/height">height</a>; the <a class="localLink" href="/statType">statType</a> <a class="localLink" href="/median">median</a>; the <a class="localLink" href="/gender">gender</a> <a class="localLink" href="/Female">Female</a>. It is important to note that there are many kinds of scientific quantitative observation which are not fully, perfectly or unambiguously described following this pattern, or with solely Schema.org terminology. The approach taken here is designed to allow partial, incremental or minimal description of <a class="localLink" href="/StatisticalVariable">StatisticalVariable</a>s, and the use of detailed sets of entity and property IDs from external repositories. The <a class="localLink" href="/measurementMethod">measurementMethod</a>, <a class="localLink" href="/unitCode">unitCode</a> and <a class="localLink" href="/unitText">unitText</a> properties can also be used to clarify the specific nature and notation of an observed measurement.');
t('<a class="localLink" href="/StatisticalVariable">StatisticalVariable</a> represents any type of statistical metric that can be measured at a place and time.');

// https://schema.org/StatusEnumeration
t("StatusEnumeration");
t("Lists or enumerations dealing with status types.");

// https://schema.org/SteeringPositionValue
t("SteeringPositionValue");
t("A value indicating a steering position.");

// https://schema.org/Store
t("Store");
t("A retail good store.");

// https://schema.org/StoreCreditRefund
t("StoreCreditRefund");
t("Specifies that the customer receives a store credit as refund when returning a product.");

// https://schema.org/StrengthTraining
t("StrengthTraining");
t("Physical activity that is engaged in to improve muscle and bone strength. Also referred to as resistance training.");
t("Physical activity that is engaged in to improve muscle and bone strength.");

// https://schema.org/StructuredValue
t("StructuredValue");
t("Structured values are used when the value of a property has a more complex structure than simply being a textual value or a reference to another thing.");

// https://schema.org/StudioAlbum
t("StudioAlbum");
t("StudioAlbum.");

// https://schema.org/StupidType
t("StupidType");
t("A StupidType for testing.");

// https://schema.org/SubscribeAction
t("SubscribeAction");
t('The act of forming a personal connection with someone/something (object) unidirectionally/asymmetrically to get updates pushed to.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/FollowAction">FollowAction</a>: Unlike FollowAction, SubscribeAction implies that the subscriber acts as a passive agent being constantly/actively pushed for updates.</li>
<li><a class="localLink" href="/RegisterAction">RegisterAction</a>: Unlike RegisterAction, SubscribeAction implies that the agent is interested in continuing receiving updates from the object.</li>
<li><a class="localLink" href="/JoinAction">JoinAction</a>: Unlike JoinAction, SubscribeAction implies that the agent is interested in continuing receiving updates from the object.</li>
</ul>');
t("The act of forming a personal connection with someone/something (object) unidirectionally/asymmetrically to get updates pushed to.");

// https://schema.org/Subscription
t("Subscription");
t("Represents the subscription pricing component of the total price for an offered product.");

// https://schema.org/Substance
t("Substance");
t("Any matter of defined composition that has discrete existence, whose origin may be biological, mineral or chemical.");

// https://schema.org/SubwayStation
t("SubwayStation");
t("A subway station.");

// https://schema.org/Suite
t("Suite");
t('A suite in a hotel or other public accommodation, denotes a class of luxury accommodations, the key feature of which is multiple rooms (source: Wikipedia, the free encyclopedia, see <a href="http://en.wikipedia.org/wiki/Suite_(hotel)">http://en.wikipedia.org/wiki/Suite_(hotel)</a>).
<br /><br />
See also the <a href="/docs/hotels.html">dedicated document on the use of schema.org for marking up hotels and other forms of accommodations</a>.');
t('A suite in a hotel or other public accommodation, denotes a class of luxury accommodations, the key feature of which is multiple rooms">http://en.wikipedia.org/wiki/Suite_(hotel)</a>).');

// https://schema.org/Sunday
t("Sunday");
t("The day of the week between Saturday and Monday.");

// https://schema.org/SuperficialAnatomy
t("SuperficialAnatomy");
t("Anatomical features that can be observed by sight (without dissection), including the form and proportions of the human body as well as surface landmarks that correspond to deeper subcutaneous structures. Superficial anatomy plays an important role in sports medicine, phlebotomy, and other medical specialties as underlying anatomical structures can be identified through surface palpation. For example, during back surgery, superficial anatomy can be used to palpate and count vertebrae to find the site of incision. Or in phlebotomy, superficial anatomy can be used to locate an underlying vein; for example, the median cubital vein can be located by palpating the borders of the cubital fossa (such as the epicondyles of the humerus) and then looking for the superficial signs of the vein, such as size, prominence, ability to refill after depression, and feel of surrounding tissue support. As another example, in a subluxation (dislocation) of the glenohumeral joint, the bony structure becomes pronounced with the deltoid muscle failing to cover the glenohumeral joint allowing the edges of the scapula to be superficially visible. Here, the superficial anatomy is the visible edges of the scapula, implying the underlying dislocation of the joint (the related anatomical structure).");
t("Anatomical features that can be observed by sight (without dissection), including the form and proportions of the human body as well as surface landmarks that correspond to deeper subcutaneous structures.");

// https://schema.org/Surgical
t("Surgical");
t("A specific branch of medical science that pertains to treating diseases, injuries and deformities by manual and instrumental means.");

// https://schema.org/SurgicalProcedure
t("SurgicalProcedure");
t("A medical procedure involving an incision with instruments; performed for diagnose, or therapeutic purposes.");

// https://schema.org/SuspendAction
t("SuspendAction");
t("The act of momentarily pausing a device or application (e.g. pause music playback or pause a timer).");

// https://schema.org/Suspended
t("Suspended");
t("Suspended.");

// https://schema.org/Syllabus
t("Syllabus");
t('A syllabus that describes the material covered in a course, often with several such sections per <a class="localLink" href="/Course">Course</a> so that a distinct <a class="localLink" href="/timeRequired">timeRequired</a> can be provided for that section of the <a class="localLink" href="/Course">Course</a>.');

// https://schema.org/SymptomsHealthAspect
t("SymptomsHealthAspect");
t("Symptoms or related symptoms of a Topic.");

// https://schema.org/Synagogue
t("Synagogue");
t("A synagogue.");

// https://schema.org/Table
t("Table");
t("A table on a Web page.");

// https://schema.org/TakeAction
t("TakeAction");
t('The act of gaining ownership of an object from an origin. Reciprocal of GiveAction.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/GiveAction">GiveAction</a>: The reciprocal of TakeAction.</li>
<li><a class="localLink" href="/ReceiveAction">ReceiveAction</a>: Unlike ReceiveAction, TakeAction implies that ownership has been transferred.</li>
</ul>');
t("The act of gaining ownership of an object from an origin.");

// https://schema.org/TattooParlor
t("TattooParlor");
t("A tattoo parlor.");

// https://schema.org/Taxi
t("Taxi");
t("A taxi.");

// https://schema.org/TaxiReservation
t("TaxiReservation");
t('A reservation for a taxi.<br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations. For offers of tickets, use <a class="localLink" href="/Offer">Offer</a>.');
t("A reservation for a taxi.");

// https://schema.org/TaxiService
t("TaxiService");
t("A service for a vehicle for hire with a driver for local travel. Fares are usually calculated based on distance traveled.");
t("A service for a vehicle for hire with a driver for local travel.");

// https://schema.org/TaxiStand
t("TaxiStand");
t("A taxi stand.");

// https://schema.org/TaxiVehicleUsage
t("TaxiVehicleUsage");
t("Indicates the usage of the car as a taxi.");

// https://schema.org/Taxon
t("Taxon");
t("A set of organisms asserted to represent a natural cohesive biological unit.");

// https://schema.org/TechArticle
t("TechArticle");
t("A technical article - Example: How-to (task) topics, step-by-step, procedural troubleshooting, specifications, etc.");

// https://schema.org/TelevisionChannel
t("TelevisionChannel");
t("A unique instance of a television BroadcastService on a CableOrSatelliteService lineup.");

// https://schema.org/TelevisionStation
t("TelevisionStation");
t("A television station.");

// https://schema.org/TennisComplex
t("TennisComplex");
t("A tennis complex.");

// https://schema.org/Terminated
t("Terminated");
t("Terminated.");

// https://schema.org/Text
t("Text");
t("Data type: Text.");

// https://schema.org/TextDigitalDocument
t("TextDigitalDocument");
t("A file composed primarily of text.");

// https://schema.org/TextObject
t("TextObject");
t("A text file. The text can be unformatted or contain markup, html, etc.");
t("A text file.");

// https://schema.org/TheaterEvent
t("TheaterEvent");
t("Event type: Theater performance.");

// https://schema.org/TheaterGroup
t("TheaterGroup");
t("A theater group or company, for example, the Royal Shakespeare Company or Druid Theatre.");

// https://schema.org/Therapeutic
t("Therapeutic");
t("A medical device used for therapeutic purposes.");

// https://schema.org/TherapeuticProcedure
t("TherapeuticProcedure");
t("A medical procedure intended primarily for therapeutic purposes, aimed at improving a health condition.");

// https://schema.org/Thesis
t("Thesis");
t("A thesis or dissertation document submitted in support of candidature for an academic degree or professional qualification.");

// https://schema.org/Thing
t("Thing");
t("The most generic type of item.");

// https://schema.org/Throat
t("Throat");
t("Throat assessment with  clinical examination.");

// https://schema.org/Thursday
t("Thursday");
t("The day of the week between Wednesday and Friday.");

// https://schema.org/Ticket
t("Ticket");
t("Used to describe a ticket to an event, a flight, a bus ride, etc.");

// https://schema.org/TieAction
t("TieAction");
t("The act of reaching a draw in a competitive activity.");

// https://schema.org/Time
t("Time");
t('A point in time recurring on multiple days in the form hh:mm:ss[Z|(+|-)hh:mm] (see <a href="http://www.w3.org/TR/xmlschema-2/#time">XML schema for details</a>).');

// https://schema.org/TipAction
t("TipAction");
t("The act of giving money voluntarily to a beneficiary in recognition of services rendered.");

// https://schema.org/TireShop
t("TireShop");
t("A tire shop.");

// https://schema.org/TobaccoNicotineConsideration
t("TobaccoNicotineConsideration");
t("Item contains tobacco and/or nicotine, for example cigars, cigarettes, chewing tobacco, e-cigarettes, or hookahs.");

// https://schema.org/TollFree
t("TollFree");
t("The associated telephone number is toll free.");

// https://schema.org/TouristAttraction
t("TouristAttraction");
t('A tourist attraction.  In principle any Thing can be a <a class="localLink" href="/TouristAttraction">TouristAttraction</a>, from a <a class="localLink" href="/Mountain">Mountain</a> and <a class="localLink" href="/LandmarksOrHistoricalBuildings">LandmarksOrHistoricalBuildings</a> to a <a class="localLink" href="/LocalBusiness">LocalBusiness</a>.  This Type can be used on its own to describe a general <a class="localLink" href="/TouristAttraction">TouristAttraction</a>, or be used as an <a class="localLink" href="/additionalType">additionalType</a> to add tourist attraction properties to any other type.  (See examples below)');
t("A tourist attraction.");

// https://schema.org/TouristDestination
t("TouristDestination");
t('A tourist destination. In principle any <a class="localLink" href="/Place">Place</a> can be a <a class="localLink" href="/TouristDestination">TouristDestination</a> from a <a class="localLink" href="/City">City</a>, Region or <a class="localLink" href="/Country">Country</a> to an <a class="localLink" href="/AmusementPark">AmusementPark</a> or <a class="localLink" href="/Hotel">Hotel</a>. This Type can be used on its own to describe a general <a class="localLink" href="/TouristDestination">TouristDestination</a>, or be used as an <a class="localLink" href="/additionalType">additionalType</a> to add tourist relevant properties to any other <a class="localLink" href="/Place">Place</a>.  A <a class="localLink" href="/TouristDestination">TouristDestination</a> is defined as a <a class="localLink" href="/Place">Place</a> that contains, or is colocated with, one or more <a class="localLink" href="/TouristAttraction">TouristAttraction</a>s, often linked by a similar theme or interest to a particular <a class="localLink" href="/touristType">touristType</a>. The <a href="http://www2.unwto.org/">UNWTO</a> defines Destination (main destination of a tourism trip) as the place visited that is central to the decision to take the trip.
  (See examples below.)');
t("A tourist destination.");

// https://schema.org/TouristInformationCenter
t("TouristInformationCenter");
t("A tourist information center.");

// https://schema.org/TouristTrip
t("TouristTrip");
t('A tourist trip. A created itinerary of visits to one or more places of interest (<a class="localLink" href="/TouristAttraction">TouristAttraction</a>/<a class="localLink" href="/TouristDestination">TouristDestination</a>) often linked by a similar theme, geographic area, or interest to a particular <a class="localLink" href="/touristType">touristType</a>. The <a href="http://www2.unwto.org/">UNWTO</a> defines tourism trip as the Trip taken by visitors.
  (See examples below.)');
t("A tourist trip.");

// https://schema.org/Toxicologic
t("Toxicologic");
t("A specific branch of medical science that is concerned with poisons, their nature, effects and detection and involved in the treatment of poisoning.");

// https://schema.org/ToyStore
t("ToyStore");
t("A toy store.");

// https://schema.org/TrackAction
t("TrackAction");
t('An agent tracks an object for updates.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/FollowAction">FollowAction</a>: Unlike FollowAction, TrackAction refers to the interest on the location of innanimates objects.</li>
<li><a class="localLink" href="/SubscribeAction">SubscribeAction</a>: Unlike SubscribeAction, TrackAction refers to  the interest on the location of innanimate objects.</li>
</ul>');
t("An agent tracks an object for updates.");

// https://schema.org/TradeAction
t("TradeAction");
t("The act of participating in an exchange of goods and services for monetary compensation. An agent trades an object, product or service with a participant in exchange for a one time or periodic payment.");
t("The act of participating in an exchange of goods and services for monetary compensation.");

// https://schema.org/TraditionalChinese
t("TraditionalChinese");
t("A system of medicine based on common theoretical concepts that originated in China and evolved over thousands of years, that uses herbs, acupuncture, exercise, massage, dietary therapy, and other methods to treat a wide range of conditions.");

// https://schema.org/TrainedAlgorithmicMediaDigitalSource
t("TrainedAlgorithmicMediaDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/trainedAlgorithmicMedia">trained algorithmic media</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/TrainReservation
t("TrainReservation");
t('A reservation for train travel.<br/><br/>

Note: This type is for information about actual reservations, e.g. in confirmation emails or HTML pages with individual confirmations of reservations. For offers of tickets, use <a class="localLink" href="/Offer">Offer</a>.');
t("A reservation for train travel.");

// https://schema.org/TrainStation
t("TrainStation");
t("A train station.");

// https://schema.org/TrainTrip
t("TrainTrip");
t("A trip on a commercial train line.");

// https://schema.org/TransferAction
t("TransferAction");
t("The act of transferring/moving (abstract or concrete) animate or inanimate objects from one place to another.");

// https://schema.org/TransformedContent
t("TransformedContent");
t('Content coded \'transformed content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.<br/><br/>

For a <a class="localLink" href="/VideoObject">VideoObject</a> to be \'transformed content\':  or all of the video has been manipulated to transform the footage itself. This category includes using tools like the Adobe Suite to change the speed of the video, add or remove visual elements or dub audio. Deepfakes are also a subset of transformation.<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> to be \'transformed content\': Adding or deleting visual elements to give the image a different meaning with the intention to mislead.<br/><br/>

For an <a class="localLink" href="/ImageObject">ImageObject</a> with embedded text to be \'transformed content\': Adding or deleting visual elements to give the image a different meaning with the intention to mislead.<br/><br/>

For an <a class="localLink" href="/AudioObject">AudioObject</a> to be \'transformed content\': Part or all of the audio has been manipulated to alter the words or sounds, or the audio has been synthetically generated, such as to create a sound-alike voice.');
t('Content coded \'transformed content\' in a <a class="localLink" href="/MediaReview">MediaReview</a>, considered in the context of how it was published or shared.');

// https://schema.org/TransitMap
t("TransitMap");
t("A transit map.");

// https://schema.org/TravelAction
t("TravelAction");
t("The act of traveling from a fromLocation to a destination by a specified mode of transport, optionally with participants.");

// https://schema.org/TravelAgency
t("TravelAgency");
t("A travel agency.");

// https://schema.org/TreatmentIndication
t("TreatmentIndication");
t("An indication for treating an underlying condition, symptom, etc.");

// https://schema.org/TreatmentsHealthAspect
t("TreatmentsHealthAspect");
t("Treatments or related therapies for a Topic.");

// https://schema.org/Trip
t("Trip");
t("A trip or journey. An itinerary of visits to one or more places.");
t("A trip or journey.");

// https://schema.org/TripleBlindedTrial
t("TripleBlindedTrial");
t("A trial design in which neither the researcher, the person administering the therapy nor the patient knows the details of the treatment the patient was randomly assigned to.");

// https://schema.org/True
t("True");
t("The boolean value true.");

// https://schema.org/Tuesday
t("Tuesday");
t("The day of the week between Monday and Wednesday.");

// https://schema.org/TVClip
t("TVClip");
t("A short TV program or a segment/part of a TV program.");

// https://schema.org/TVEpisode
t("TVEpisode");
t("A TV episode which can be part of a series or season.");

// https://schema.org/TVSeason
t("TVSeason");
t("Season dedicated to TV broadcast and associated online delivery.");

// https://schema.org/TVSeries
t("TVSeries");
t("CreativeWorkSeries dedicated to TV broadcast and associated online delivery.");

// https://schema.org/TypeAndQuantityNode
t("TypeAndQuantityNode");
t("A structured value indicating the quantity, unit of measurement, and business function of goods included in a bundle offer.");

// https://schema.org/TypesHealthAspect
t("TypesHealthAspect");
t("Categorization and other types related to a topic.");

// https://schema.org/UKNonprofitType
t("UKNonprofitType");
t("UKNonprofitType: Non-profit organization type originating from the United Kingdom.");

// https://schema.org/UKTrust
t("UKTrust");
t("UKTrust: Non-profit type referring to a UK trust.");

// https://schema.org/Ultrasound
t("Ultrasound");
t("Ultrasound imaging.");

// https://schema.org/UnclassifiedAdultConsideration
t("UnclassifiedAdultConsideration");
t('The item is suitable only for adults, without indicating why. Due to widespread use of "adult" as a euphemism for "sexual", many such items are likely suited also for the SexualContentConsideration code.');
t("The item is suitable only for adults, without indicating why.");

// https://schema.org/UnemploymentSupport
t("UnemploymentSupport");
t("UnemploymentSupport: this is a benefit for unemployment support.");

// https://schema.org/UnincorporatedAssociationCharity
t("UnincorporatedAssociationCharity");
t("UnincorporatedAssociationCharity: Non-profit type referring to a charitable company that is not incorporated (UK).");

// https://schema.org/UnitPriceSpecification
t("UnitPriceSpecification");
t("The price asked for a given offer by the respective organization or person.");

// https://schema.org/UnofficialLegalValue
t("UnofficialLegalValue");
t("Indicates that a document has no particular or special standing (e.g. a republication of a law by a private publisher).");

// https://schema.org/UnRegisterAction
t("UnRegisterAction");
t('The act of un-registering from a service.<br/><br/>

Related actions:<br/><br/>

<ul>
<li><a class="localLink" href="/RegisterAction">RegisterAction</a>: antonym of UnRegisterAction.</li>
<li><a class="localLink" href="/LeaveAction">LeaveAction</a>: Unlike LeaveAction, UnRegisterAction implies that you are unregistering from a service you were previously registered, rather than leaving a team/group of people.</li>
</ul>');
t("The act of un-registering from a service.");

// https://schema.org/UpdateAction
t("UpdateAction");
t("The act of managing by changing/editing the state of the object.");

// https://schema.org/URL
t("URL");
t("Data type: URL.");

// https://schema.org/Urologic
t("Urologic");
t("A specific branch of medical science that is concerned with the diagnosis and treatment of diseases pertaining to the urinary tract and the urogenital system.");

// https://schema.org/UsageOrScheduleHealthAspect
t("UsageOrScheduleHealthAspect");
t("Content about how, when, frequency and dosage of a topic.");

// https://schema.org/UseAction
t("UseAction");
t("The act of applying an object to its intended purpose.");

// https://schema.org/UsedCondition
t("UsedCondition");
t("Indicates that the item is used.");

// https://schema.org/UserBlocks
t("UserBlocks");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserCheckins
t("UserCheckins");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserComments
t("UserComments");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserDownloads
t("UserDownloads");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserInteraction
t("UserInteraction");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserLikes
t("UserLikes");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserPageVisits
t("UserPageVisits");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserPlays
t("UserPlays");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserPlusOnes
t("UserPlusOnes");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/UserReview
t("UserReview");
t('A review created by an end-user (e.g. consumer, purchaser, attendee etc.), in contrast with <a class="localLink" href="/CriticReview">CriticReview</a>.');

// https://schema.org/UserTweets
t("UserTweets");
t('UserInteraction and its subtypes is an old way of talking about users interacting with pages. It is generally better to use <a class="localLink" href="/Action">Action</a>-based vocabulary, alongside types such as <a class="localLink" href="/Comment">Comment</a>.');
t("UserInteraction and its subtypes is an old way of talking about users interacting with pages.");

// https://schema.org/USNonprofitType
t("USNonprofitType");
t("USNonprofitType: Non-profit organization type originating from the United States.");

// https://schema.org/VacationRental
t("VacationRental");
t("A kind of lodging business that focuses on renting single properties for limited time.");

// https://schema.org/VeganDiet
t("VeganDiet");
t("A diet exclusive of all animal products.");

// https://schema.org/VegetarianDiet
t("VegetarianDiet");
t("A diet exclusive of animal meat.");

// https://schema.org/Vehicle
t("Vehicle");
t("A vehicle is a device that is designed or used to transport people or cargo over land, water, air, or through space.");

// https://schema.org/Vein
t("Vein");
t("A type of blood vessel that specifically carries blood to the heart.");

// https://schema.org/VenueMap
t("VenueMap");
t("A venue map (e.g. for malls, auditoriums, museums, etc.).");

// https://schema.org/Vessel
t("Vessel");
t("A component of the human body circulatory system comprised of an intricate network of hollow tubes that transport blood throughout the entire body.");

// https://schema.org/VeterinaryCare
t("VeterinaryCare");
t("A vet's office.");

// https://schema.org/VideoGallery
t("VideoGallery");
t("Web page type: Video gallery page.");

// https://schema.org/VideoGame
t("VideoGame");
t("A video game is an electronic game that involves human interaction with a user interface to generate visual feedback on a video device.");

// https://schema.org/VideoGameClip
t("VideoGameClip");
t("A short segment/part of a video game.");

// https://schema.org/VideoGameSeries
t("VideoGameSeries");
t("A video game series.");

// https://schema.org/VideoObject
t("VideoObject");
t("A video file.");

// https://schema.org/VideoObjectSnapshot
t("VideoObjectSnapshot");
t('A specific and exact (byte-for-byte) version of a <a class="localLink" href="/VideoObject">VideoObject</a>. Two byte-for-byte identical files, for the purposes of this type, considered identical. If they have different embedded metadata the files will differ. Different external facts about the files, e.g. creator or dateCreated that aren\'t represented in their actual content, do not affect this notion of identity.');
t('A specific and exact (byte-for-byte) version of a <a class="localLink" href="/VideoObject">VideoObject</a>.');

// https://schema.org/ViewAction
t("ViewAction");
t("The act of consuming static visual content.");

// https://schema.org/VinylFormat
t("VinylFormat");
t("VinylFormat.");

// https://schema.org/ViolenceConsideration
t("ViolenceConsideration");
t("Item shows or promotes violence.");

// https://schema.org/VirtualLocation
t("VirtualLocation");
t("An online or virtual location for attending events. For example, one may attend an online seminar or educational event. While a virtual location may be used as the location of an event, virtual locations should not be confused with physical locations in the real world.");
t("An online or virtual location for attending events.");

// https://schema.org/VirtualRecordingDigitalSource
t("VirtualRecordingDigitalSource");
t('Content coded as \'<a href="https://cv.iptc.org/newscodes/digitalsourcetype/virtualRecording">virtual recording</a>\' using the IPTC <a href="https://cv.iptc.org/newscodes/digitalsourcetype/">digital source type</a> vocabulary.');

// https://schema.org/Virus
t("Virus");
t("Pathogenic virus that causes viral infection.");

// https://schema.org/VisualArtsEvent
t("VisualArtsEvent");
t("Event type: Visual arts event.");

// https://schema.org/VisualArtwork
t("VisualArtwork");
t("A work of art that is primarily visual in character.");

// https://schema.org/VitalSign
t("VitalSign");
t("Vital signs are measures of various physiological functions in order to assess the most basic body functions.");

// https://schema.org/Volcano
t("Volcano");
t("A volcano, like Fujisan.");

// https://schema.org/VoteAction
t("VoteAction");
t("The act of expressing a preference from a fixed/finite/structured set of choices/options.");

// https://schema.org/WantAction
t("WantAction");
t("The act of expressing a desire about the object. An agent wants an object.");
t("The act of expressing a desire about the object.");

// https://schema.org/WarrantyPromise
t("WarrantyPromise");
t("A structured value representing the duration and scope of services that will be provided to a customer free of charge in case of a defect or malfunction of a product.");

// https://schema.org/WarrantyScope
t("WarrantyScope");
t("A range of services that will be provided to a customer free of charge in case of a defect or malfunction of a product.<br/><br/>

Commonly used values:<br/><br/>

<ul>
<li>http://purl.org/goodrelations/v1#Labor-BringIn</li>
<li>http://purl.org/goodrelations/v1#PartsAndLabor-BringIn</li>
<li>http://purl.org/goodrelations/v1#PartsAndLabor-PickUp</li>
</ul>");
t("A range of services that will be provided to a customer free of charge in case of a defect or malfunction of a product.");

// https://schema.org/WatchAction
t("WatchAction");
t("The act of consuming dynamic/moving visual content.");

// https://schema.org/Waterfall
t("Waterfall");
t("A waterfall, like Niagara.");

// https://schema.org/WeaponConsideration
t("WeaponConsideration");
t("The item is intended to induce bodily harm, for example guns, mace, combat knives, brass knuckles, nail or other bombs, and spears.");

// https://schema.org/WearableMeasurementBack
t("WearableMeasurementBack");
t("Measurement of the back section, for example of a jacket.");

// https://schema.org/WearableMeasurementChestOrBust
t("WearableMeasurementChestOrBust");
t("Measurement of the chest/bust section, for example of a suit.");

// https://schema.org/WearableMeasurementCollar
t("WearableMeasurementCollar");
t("Measurement of the collar, for example of a shirt.");

// https://schema.org/WearableMeasurementCup
t("WearableMeasurementCup");
t("Measurement of the cup, for example of a bra.");

// https://schema.org/WearableMeasurementHeight
t("WearableMeasurementHeight");
t("Measurement of the height, for example the heel height of a shoe.");

// https://schema.org/WearableMeasurementHips
t("WearableMeasurementHips");
t("Measurement of the hip section, for example of a skirt.");

// https://schema.org/WearableMeasurementInseam
t("WearableMeasurementInseam");
t("Measurement of the inseam, for example of pants.");

// https://schema.org/WearableMeasurementLength
t("WearableMeasurementLength");
t("Represents the length, for example of a dress.");

// https://schema.org/WearableMeasurementOutsideLeg
t("WearableMeasurementOutsideLeg");
t("Measurement of the outside leg, for example of pants.");

// https://schema.org/WearableMeasurementSleeve
t("WearableMeasurementSleeve");
t("Measurement of the sleeve length, for example of a shirt.");

// https://schema.org/WearableMeasurementTypeEnumeration
t("WearableMeasurementTypeEnumeration");
t("Enumerates common types of measurement for wearables products.");

// https://schema.org/WearableMeasurementWaist
t("WearableMeasurementWaist");
t("Measurement of the waist section, for example of pants.");

// https://schema.org/WearableMeasurementWidth
t("WearableMeasurementWidth");
t("Measurement of the width, for example of shoes.");

// https://schema.org/WearableSizeGroupBig
t("WearableSizeGroupBig");
t('Size group "Big" for wearables.');

// https://schema.org/WearableSizeGroupBoys
t("WearableSizeGroupBoys");
t('Size group "Boys" for wearables.');

// https://schema.org/WearableSizeGroupEnumeration
t("WearableSizeGroupEnumeration");
t('Enumerates common size groups (also known as "size types") for wearable products.');

// https://schema.org/WearableSizeGroupExtraShort
t("WearableSizeGroupExtraShort");
t('Size group "Extra Short" for wearables.');

// https://schema.org/WearableSizeGroupExtraTall
t("WearableSizeGroupExtraTall");
t('Size group "Extra Tall" for wearables.');

// https://schema.org/WearableSizeGroupGirls
t("WearableSizeGroupGirls");
t('Size group "Girls" for wearables.');

// https://schema.org/WearableSizeGroupHusky
t("WearableSizeGroupHusky");
t('Size group "Husky" (or "Stocky") for wearables.');

// https://schema.org/WearableSizeGroupInfants
t("WearableSizeGroupInfants");
t('Size group "Infants" for wearables.');

// https://schema.org/WearableSizeGroupJuniors
t("WearableSizeGroupJuniors");
t('Size group "Juniors" for wearables.');

// https://schema.org/WearableSizeGroupMaternity
t("WearableSizeGroupMaternity");
t('Size group "Maternity" for wearables.');

// https://schema.org/WearableSizeGroupMens
t("WearableSizeGroupMens");
t('Size group "Mens" for wearables.');

// https://schema.org/WearableSizeGroupMisses
t("WearableSizeGroupMisses");
t('Size group "Misses" (also known as "Missy") for wearables.');

// https://schema.org/WearableSizeGroupPetite
t("WearableSizeGroupPetite");
t('Size group "Petite" for wearables.');

// https://schema.org/WearableSizeGroupPlus
t("WearableSizeGroupPlus");
t('Size group "Plus" for wearables.');

// https://schema.org/WearableSizeGroupRegular
t("WearableSizeGroupRegular");
t('Size group "Regular" for wearables.');

// https://schema.org/WearableSizeGroupShort
t("WearableSizeGroupShort");
t('Size group "Short" for wearables.');

// https://schema.org/WearableSizeGroupTall
t("WearableSizeGroupTall");
t('Size group "Tall" for wearables.');

// https://schema.org/WearableSizeGroupWomens
t("WearableSizeGroupWomens");
t('Size group "Womens" for wearables.');

// https://schema.org/WearableSizeSystemAU
t("WearableSizeSystemAU");
t("Australian size system for wearables.");

// https://schema.org/WearableSizeSystemBR
t("WearableSizeSystemBR");
t("Brazilian size system for wearables.");

// https://schema.org/WearableSizeSystemCN
t("WearableSizeSystemCN");
t("Chinese size system for wearables.");

// https://schema.org/WearableSizeSystemContinental
t("WearableSizeSystemContinental");
t("Continental size system for wearables.");

// https://schema.org/WearableSizeSystemDE
t("WearableSizeSystemDE");
t("German size system for wearables.");

// https://schema.org/WearableSizeSystemEN13402
t("WearableSizeSystemEN13402");
t("EN 13402 (joint European standard for size labelling of clothes).");

// https://schema.org/WearableSizeSystemEnumeration
t("WearableSizeSystemEnumeration");
t("Enumerates common size systems specific for wearable products.");

// https://schema.org/WearableSizeSystemEurope
t("WearableSizeSystemEurope");
t("European size system for wearables.");

// https://schema.org/WearableSizeSystemFR
t("WearableSizeSystemFR");
t("French size system for wearables.");

// https://schema.org/WearableSizeSystemGS1
t("WearableSizeSystemGS1");
t("GS1 (formerly NRF) size system for wearables.");

// https://schema.org/WearableSizeSystemIT
t("WearableSizeSystemIT");
t("Italian size system for wearables.");

// https://schema.org/WearableSizeSystemJP
t("WearableSizeSystemJP");
t("Japanese size system for wearables.");

// https://schema.org/WearableSizeSystemMX
t("WearableSizeSystemMX");
t("Mexican size system for wearables.");

// https://schema.org/WearableSizeSystemUK
t("WearableSizeSystemUK");
t("United Kingdom size system for wearables.");

// https://schema.org/WearableSizeSystemUS
t("WearableSizeSystemUS");
t("United States size system for wearables.");

// https://schema.org/WearAction
t("WearAction");
t("The act of dressing oneself in clothing.");

// https://schema.org/WebAPI
t("WebAPI");
t("An application programming interface accessible over Web/Internet technologies.");

// https://schema.org/WebApplication
t("WebApplication");
t("Web applications.");

// https://schema.org/WebContent
t("WebContent");
t('WebContent is a type representing all <a class="localLink" href="/WebPage">WebPage</a>, <a class="localLink" href="/WebSite">WebSite</a> and <a class="localLink" href="/WebPageElement">WebPageElement</a> content. It is sometimes the case that detailed distinctions between Web pages, sites and their parts are not always important or obvious. The  <a class="localLink" href="/WebContent">WebContent</a> type makes it easier to describe Web-addressable content without requiring such distinctions to always be stated. (The intent is that the existing types <a class="localLink" href="/WebPage">WebPage</a>, <a class="localLink" href="/WebSite">WebSite</a> and <a class="localLink" href="/WebPageElement">WebPageElement</a> will eventually be declared as subtypes of <a class="localLink" href="/WebContent">WebContent</a>.)');
t('WebContent is a type representing all <a class="localLink" href="/WebPage">WebPage</a>, <a class="localLink" href="/WebSite">WebSite</a> and <a class="localLink" href="/WebPageElement">WebPageElement</a> content.');

// https://schema.org/WebPage
t("WebPage");
t("A web page. Every web page is implicitly assumed to be declared to be of type WebPage, so the various properties about that webpage, such as <code>breadcrumb</code> may be used. We recommend explicit declaration if these properties are specified, but if they are found outside of an itemscope, they will be assumed to be about the page.");
t("A web page.");

// https://schema.org/WebPageElement
t("WebPageElement");
t("A web page element, like a table or an image.");

// https://schema.org/WebSite
t("WebSite");
t("A WebSite is a set of related web pages and other items typically served from a single web domain and accessible via URLs.");

// https://schema.org/Wednesday
t("Wednesday");
t("The day of the week between Tuesday and Thursday.");

// https://schema.org/WesternConventional
t("WesternConventional");
t("The conventional Western system of medicine, that aims to apply the best available evidence gained from the scientific method to clinical decision making. Also known as conventional or Western medicine.");
t("The conventional Western system of medicine, that aims to apply the best available evidence gained from the scientific method to clinical decision making.");

// https://schema.org/Wholesale
t("Wholesale");
t("The drug's cost represents the wholesale acquisition cost of the drug.");

// https://schema.org/WholesaleStore
t("WholesaleStore");
t("A wholesale store.");

// https://schema.org/WinAction
t("WinAction");
t("The act of achieving victory in a competitive activity.");

// https://schema.org/Winery
t("Winery");
t("A winery.");

// https://schema.org/Withdrawn
t("Withdrawn");
t("Withdrawn.");

// https://schema.org/WorkBasedProgram
t("WorkBasedProgram");
t("A program with both an educational and employment component. Typically based at a workplace and structured around work-based learning, with the aim of instilling competencies related to an occupation. WorkBasedProgram is used to distinguish programs such as apprenticeships from school, college or other classroom based educational programs.");
t("A program with both an educational and employment component.");

// https://schema.org/WorkersUnion
t("WorkersUnion");
t("A Workers Union (also known as a Labor Union, Labour Union, or Trade Union) is an organization that promotes the interests of its worker members by collectively bargaining with management, organizing, and political lobbying.");

// https://schema.org/WPAdBlock
t("WPAdBlock");
t("An advertising section of the page.");

// https://schema.org/WPFooter
t("WPFooter");
t("The footer section of the page.");

// https://schema.org/WPHeader
t("WPHeader");
t("The header section of the page.");

// https://schema.org/WPSideBar
t("WPSideBar");
t("A sidebar section of the page.");

// https://schema.org/WriteAction
t("WriteAction");
t("The act of authoring written creative content.");

// https://schema.org/WritePermission
t("WritePermission");
t("Permission to write or edit the document.");

// https://schema.org/XPathType
t("XPathType");
t("Text representing an XPath (typically but not necessarily version 1.0).");

// https://schema.org/XRay
t("XRay");
t("X-ray imaging.");

// https://schema.org/ZoneBoardingPolicy
t("ZoneBoardingPolicy");
t("The airline boards by zones of the plane.");

// https://schema.org/Zoo
t("Zoo");
t("A zoo.");
