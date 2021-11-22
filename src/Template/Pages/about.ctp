<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<h2>
    Stats
</h2>
<div class="row">
    <div class="offset-sm-2 col-sm-8">
        <table id="stats" class="table">
            <tbody>
                <?php foreach ($stats as $label => $value): ?>
                    <tr>
                        <th>
                            <?= $label ?>
                        </th>
                        <td>
                            <?= $value ?>
                        <td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<h2>
    Q & A
</h2>
<div class="row" id="faq">
    <div class="offset-sm-2 col-sm-8">
        <section>
            <p>
                Here, people share Thoughts. Each Thought is located under a single word that it relates to, and each Thinker is identified by only a unique color.
                Ether has collected <?= number_format($thoughtCount) ?> Thoughts from <?= number_format($thinkerCount) ?> Thinkers since 2006.
            </p>
        </section>

        <section>
            <h2>
                Ether
            </h2>

            <dl>
                <dt>
                    <a href="#">
                        What is this?
                    </a>
                </dt>
                <dd>
                    <strong>
                        Answer A:
                    </strong>
                    <p>
                        Ether is an experimental website to read and post thoughts in anonymity. The "subject" of each post is a single word.
                    </p>

                    <strong>
                        Answer B:
                    </strong>
                    <p>
                        Ether is an abstract dimension of space populated by Thoughts. The location of a Thought is determined by alphanumeric coordinates, which spell out Thoughtwords. Inside Ether, you are navigating around the collective minds of all of its contributors.
                    </p>

                    <strong>
                        Answer C:
                    </strong>
                    <p>
                        If the social forces that pressure us to tailor our expression to a critical audience are removed, what will happen to our expression? Would we unlearn superficiality and re-learn sincerity? Would we discover things about our true selves after giving up on constantly reinventing false selves? If this were a collaborative experiment, would our mutual sincere experimental expression show us something about the nature of humanity?
                    </p>

                    <strong>
                        Answer D:
                    </strong>
                    <p>
                        *shrug*
                    </p>
                </dd>

                <dt>
                    <a href="#">
                        Who made this?
                    </a>
                </dt>
                <dd>
                    <a href="mailto:graham@phantomwatson.com">Graham "Phantom" Watson</a>
                </dd>

                <dt>
                    <a href="#">
                        Is there a glossary of Ether terms?
                    </a>
               </dt>
               <dd>
                    We do seem to throw around our own invented terms for the sake of creating atmosphere.
                    So let's go with these definitions and capitalizations for now:
                    <ul>
                        <li>
                            <strong>'Ether' or 'the Ether'</strong> - The abstract, web-based medium that contains Thoughts.
                        </li>
                        <li>
                            <strong>'Thought'</strong> - A posting made to Ether
                        </li>
                        <li>
                            <strong>'Thoughtword'</strong> - A 'location' in Ether described by a word
                        </li>
                        <li>
                            <strong>'populated Thoughtword'</strong> - A Thoughtword where Thoughts can be found
                        </li>
                        <li>
                            <strong>'unpopulated Thoughtword'</strong> - A Thoughtword with no Thoughts
                        </li>
                        <li>
                            <strong>'Thinker' or 'color'</strong> - A source of Thoughts (which could be a single person or a group)
                        </li>
                    </ul>
              </dd>
           </dl>
        </section>

        <section>
            <h2>
                Thinkers
            </h2>

            <dl>
                <dt>
                    <a href="#">
                        What's up with the little colored boxes?
                    </a>
                </dt>
                <dd>
                    Each person with an account on Ether is represented by their own color.
                    Think of each color as being someone's name. To help differentiate between similar colors,
                    each color's hexadecimal value (e.g. #123abc) is also shown in various places. You can usually click on a
                    Thinker's color to go to his or her profile page.
                </dd>

                <dt>
                    <a href="#">
                        Why are some colored boxes blank with dashed borders?
                    </a>
                </dt>
                <dd>
                    Those boxes represent Thinkers who are posting anonymously.
                </dd>

                <dt>
                    <a href="#">
                        Can multiple people use a single account/color, or a single person use multiple accounts/colors?
                    </a>
                </dt>
                <dd>
                    Absolutely, but unique email addresses are required for each account.
                </dd>

                <dt>
                    <a href="#">
                        I'm a Thinker. Am I not allowed to say who I am?
                    </a>
                </dt>
                <dd>
                    You're free to disclose as much of yourself as you wish. Ether is set up so that Thinkers
                    don't have to go out of their way to be anonymous, but doesn't at all demand that they keep
                    themselves anonymous.
                </dd>

                <dt>
                    <a href="#">
                        Hey! I think I know who that is!
                    </a>
                </dt>
                <dd>
                    It's proper netiquette to respect online anonymity. Thinkers have an opportunity to reveal
                    their identities if they wish to do so, and if a particular Thinker hasn't, then it's in very
                    poor form to threaten or take away his or her anonymity in any way, either through action
                    online or in person. Doing so disrupts the atmosphere of Ether and is likely to make the
                    person uncomfortable. So don't be a dick.
                </dd>

                <dt>
                    <a href="#">
                        Can I change my color?
                    </a>
                </dt>
                <dd>
                    At the moment, no. People like being able to recognize the colors of various Thinkers, and that
                    gets ruined when everyone's changing their colors all the time. There's a chance that
                    <a href="mailto:graham@phantomwatson.com">Phantom</a> will change your color if you ask him nicely,
                    and you're always free to just register another account with a different email address.
                </dd>

                <dt>
                    <a href="#">
                        Can I delete my account?
                    </a>
                </dt>
                <dd>
                    At the moment, no. But you can manually delete all of your thoughts and turn off your ability to
                    receive messages from other Thinkers, which is basically the same.
                </dd>

                <dt>
                    <a href="#">
                        Will you tell me who this thinker is?
                    </a>
                </dt>
                <dd>
                    The identity of Thinkers won't be revealed, nor speculation confirmed or denied, unless legally compelled.
                </dd>
            </dl>
        </section>

        <section>
            <h2>
                Thoughts
            </h2>

            <dl>
                <dt>
                    <a href="#">
                        What are the rules about posting Thoughts?
                    </a>
                </dt>
                <dd>
                    Naturally, Ether shuns rules. But here's what's encouraged:
                    <ul>
                        <li>
                            Significant thoughts
                        </li>
                        <li>
                            Introspective musing
                        </li>
                        <li>
                            Poetry
                        </li>
                        <li>
                            Personal anecdotes
                        </li>
                        <li>
                            Philosophy
                        </li>
                        <li>
                            Heartfelt confessions
                        </li>
                        <li>
                            Spilling your soul
                        </li>
                        <li>
                            A direct transcription of your thoughts associated with a word
                        </li>
                    </ul>
                    What's discouraged:
                    <ul>
                        <li>
                            Writing that is unrelated to its Thoughtword
                        </li>
                        <li>
                            Gratuitous profanity or gratuitous graphic depictions of sex or violence
                        </li>
                        <li>
                            Quoting other writers without attribution
                        </li>
                        <li>
                            Posting copyrighted material without permission
                        </li>
                        <li>
                            Making reference to other Thoughts or Thinkers in Ether (comments and messages are better for this)
                        </li>
                        <li>
                            Libellous remarks about actual, identified people
                        </li>
                        <li>
                            Advertising
                        </li>
                        <li>
                            Antagonizing
                        </li>
                        <li>
                            Terrible spelling, grammar, punctuation, etc.
                        </li>
                    </ul>
                    Also, Ether is currently an English-language-only website.
                </dd>

                <dt>
                    <a href="#">
                        How do I style my thoughts with Markdown?
                    </a>
                </dt>
                <dd>
                        A list of all the supported styles (bold, italics, lists, etc.) can be found on the
                        <?= $this->Html->link(
                            'Markdown guide',
                            [
                                'controller' => 'Pages',
                                'action' => 'markdown'
                            ]
                        ) ?>. Markdown is also supported in comments, messages, and "introspection" profile text.
                </dd>

                <dt>
                    <a href="#">
                        Why can't I comment on some thoughts?
                    </a>
                </dt>
                <dd>
                    The author of each thought can choose whether or not to enable comments.
               </dd>
            </dl>
        </section>

        <section>
            <h2>
                Thoughtwords
            </h2>

            <dl>
                <dt>
                    <a href="#">
                        What restrictions are placed on Thoughtwords?
                    </a>
                </dt>
                <dd>
                    Thoughtwords must consist of only letters from the standard English alphabet and numbers, and must be 30 characters long or less.
                </dd>

                <dt>
                    <a href="#">
                        Can Thoughtwords have punctuation in them?
                    </a>
                </dt>
                <dd>
                    <p>
                        Yes and no. Any punctuation in a Thoughtword gets stripped out (e.g. "can't" becomes "cant"), but punctuation is ignored when Thoughtwords
                        are automatically linked in the body of a Thought (e.g. "can't" gets linked to Thoughts under "cant").
                    </p>
                    <p>
                        Confusing? Basically, enter whatever contraction or hyphenated word you want for a Thoughtword and it will probably work how you expect it to.
                    </p>
                </dd>

                <dt>
                    <a href="#">
                        Can we have multi-word Thoughtwords?
                    </a>
                </dt>
                <dd>
                    <p>
                        No and yes. The system for automatically generating links to Thoughtwords depends on each of them only being a single word.
                        Otherwise, it would be ambiguous whether to link individual words or entire phrases, and common phrases (e.g. "why not") would
                        steal links from common words (e.g. "why" and "not").
                    </p>
                    <p>
                        You can use a Thoughtword like "whynot", but since that word is unlikely to be in the body of any Thought, it wouldn't benefit from the
                        automatic Thoughtword linking system.
                    </p>
                </dd>
            </dl>
        </section>

        <section>
            <h2>
                Other
            </h2>

            <dl>
                <dt>
                    <a href="#">
                        What if I have more unanswered questions?
                    </a>
                </dt>
                <dd>
                    Just <a href="mailto:graham@phantomwatson.com">shoot an email to Phantom</a>.
                </dd>
            </dl>
        </section>
    </div>
</div>

<?php $this->append('buffered_js'); ?>
    $('#faq dt a').click(function (event) {
        event.preventDefault();
        $(this).parents('dt').next('dd').slideToggle(200);
    });
<?php $this->end(); ?>
