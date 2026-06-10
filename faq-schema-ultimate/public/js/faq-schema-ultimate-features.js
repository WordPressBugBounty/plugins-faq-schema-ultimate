( function () {
	'use strict';

	// CSS Selectors
	var FAQ_CONTAINER = '#faqsu-faq-list';
	var FAQ_ITEM = '.faqsu-faq-single';
	var FAQ_QUESTION = '.faqsu-faq-question';
	var FAQ_ANSWER = '.faqsu-faq-answare';
	var STORAGE_KEY = 'faqsu_read_faqs';

	function init() {
		var container = document.querySelector( FAQ_CONTAINER );
		if ( !container ) return;

		injectToolbar( container );
		initReadingProgress( container );
		initSmartSearch( container );
		initVoiceSearch();
		initQRCodes( container );
		initPDFExport();
		handleUrlHash();
	}

	// === Inject Toolbar UI ===
	function injectToolbar( container ) {
		var toolbar = document.createElement( 'div' );
		toolbar.className = 'faqsu-features-toolbar';
		toolbar.innerHTML =
			'<div class="faqsu-toolbar-row">' +
				'<div class="faqsu-search-wrap">' +
					'<input type="text" class="faqsu-search-input" placeholder="Search FAQs...">' +
					'<button class="faqsu-voice-btn" title="Voice Search" type="button" aria-label="Voice Search">' +
						'<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/><path d="M19 11c0 3.87-3.13 7-7 7s-7-3.13-7-7H3c0 4.97 4.03 9 9 9s9-4.03 9-9h-2z"/></svg>' +
					'</button>' +
				'</div>' +
				'<button class="faqsu-pdf-btn" type="button">' +
					'<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>' +
					' Download PDF' +
				'</button>' +
			'</div>' +
			'<div class="faqsu-progress-wrap">' +
				'<span class="faqsu-progress-label">0 of 0 FAQs read</span>' +
				'<div class="faqsu-progress-bar"><div class="faqsu-progress-fill"></div></div>' +
			'</div>';
		container.parentNode.insertBefore( toolbar, container );
	}

	// === Reading Progress ===
	function initReadingProgress( container ) {
		var items = container.querySelectorAll( FAQ_ITEM );
		var total = items.length;

		function getRead() {
			try { return JSON.parse( localStorage.getItem( STORAGE_KEY ) ) || []; }
			catch ( e ) { return []; }
		}
		function saveRead( list ) {
			try { localStorage.setItem( STORAGE_KEY, JSON.stringify( list ) ); }
			catch ( e ) {}
		}
		function getId( item, index ) {
			var q = item.querySelector( FAQ_QUESTION );
			var text = q ? q.textContent.trim().substring( 0, 50 ) : 'faq-' + index;
			var hash = 0;
			for ( var i = 0; i < text.length; i++ ) {
				hash = ( ( hash << 5 ) - hash ) + text.charCodeAt( i );
				hash |= 0;
			}
			return 'faq-' + Math.abs( hash );
		}
		function updateProgress() {
			var read = getRead();
			var count = 0;
			items.forEach( function ( item, idx ) {
				var id = getId( item, idx );
				if ( read.indexOf( id ) !== -1 ) {
					count++;
					item.classList.add( 'faqsu-faq-read' );
				}
			} );
			var label = document.querySelector( '.faqsu-progress-label' );
			var fill = document.querySelector( '.faqsu-progress-fill' );
			if ( label ) label.textContent = count + ' of ' + total + ' FAQs read';
			if ( fill ) fill.style.width = ( total > 0 ? Math.round( count / total * 100 ) : 0 ) + '%';
		}

		items.forEach( function ( item, idx ) {
			item.addEventListener( 'click', function () {
				var id = getId( item, idx );
				var read = getRead();
				if ( read.indexOf( id ) === -1 ) {
					read.push( id );
					saveRead( read );
					updateProgress();
				}
			} );
		} );

		updateProgress();
	}

	// === Smart Search ===
	function initSmartSearch( container ) {
		var input = document.querySelector( '.faqsu-search-input' );
		if ( !input ) return;

		var items = container.querySelectorAll( FAQ_ITEM );

		var synonyms = {
			'refund': [ 'money back', 'return money', 'cash back', 'reimburse' ],
			'shipping': [ 'delivery', 'ship', 'transport', 'shipment' ],
			'broken': [ 'defective', 'damaged', 'faulty', 'not working' ],
			'cancel': [ 'stop', 'end', 'terminate', 'unsubscribe' ],
			'price': [ 'cost', 'fee', 'charge', 'pricing' ],
			'login': [ 'sign in', 'log in', 'access', 'authenticate' ],
			'help': [ 'support', 'assistance', 'contact' ],
			'install': [ 'setup', 'configure', 'add' ]
		};

		function expandQuery( q ) {
			var expanded = [ q.toLowerCase() ];
			Object.keys( synonyms ).forEach( function ( key ) {
				if ( q.toLowerCase().indexOf( key ) !== -1 ) {
					expanded = expanded.concat( synonyms[ key ] );
				}
				synonyms[ key ].forEach( function ( syn ) {
					if ( q.toLowerCase().indexOf( syn ) !== -1 ) {
						expanded.push( key );
					}
				} );
			} );
			return expanded;
		}

		function highlightText( elem, query ) {
			if ( !query ) {
				elem.innerHTML = elem.textContent;
				return;
			}
			var text = elem.textContent;
			var regex = new RegExp( '(' + query.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ) + ')', 'gi' );
			elem.innerHTML = text.replace( regex, '<mark class="faqsu-highlight">$1</mark>' );
		}

		input.addEventListener( 'input', function ( e ) {
			var query = e.target.value.trim();
			var queries = query ? expandQuery( query ) : [];
			var visibleCount = 0;

			items.forEach( function ( item ) {
				var q = item.querySelector( FAQ_QUESTION );
				var a = item.querySelector( FAQ_ANSWER );
				var qText = q ? q.textContent.toLowerCase() : '';
				var aText = a ? a.textContent.toLowerCase() : '';

				if ( !query ) {
					item.style.display = '';
					if ( q ) q.innerHTML = q.textContent;
					visibleCount++;
					return;
				}

				var matches = queries.some( function ( query ) {
					return qText.indexOf( query ) !== -1 || aText.indexOf( query ) !== -1;
				} );

				if ( matches ) {
					item.style.display = '';
					if ( q ) highlightText( q, query );
					visibleCount++;
				} else {
					item.style.display = 'none';
					if ( q ) q.innerHTML = q.textContent;
				}
			} );

			var noResults = document.querySelector( '.faqsu-no-results' );
			if ( visibleCount === 0 && query ) {
				if ( !noResults ) {
					noResults = document.createElement( 'div' );
					noResults.className = 'faqsu-no-results';
					noResults.textContent = 'No FAQs match your search. Try different keywords.';
					container.parentNode.insertBefore( noResults, container.nextSibling );
				}
			} else if ( noResults ) {
				noResults.remove();
			}
		} );
	}

	// === Voice Search ===
	function initVoiceSearch() {
		var btn = document.querySelector( '.faqsu-voice-btn' );
		var input = document.querySelector( '.faqsu-search-input' );
		if ( !btn || !input ) return;

		var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
		if ( !SpeechRecognition ) {
			btn.style.display = 'none';
			return;
		}

		var recognition = new SpeechRecognition();
		recognition.lang = 'en-US';
		recognition.continuous = false;
		recognition.interimResults = false;
		var isListening = false;

		btn.addEventListener( 'click', function () {
			if ( isListening ) {
				recognition.stop();
				return;
			}
			try {
				recognition.start();
				isListening = true;
				btn.classList.add( 'faqsu-listening' );
			} catch ( e ) {}
		} );

		recognition.onresult = function ( event ) {
			var transcript = event.results[ 0 ][ 0 ].transcript;
			input.value = transcript;
			input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
		};
		recognition.onend = function () {
			isListening = false;
			btn.classList.remove( 'faqsu-listening' );
		};
		recognition.onerror = function ( event ) {
			isListening = false;
			btn.classList.remove( 'faqsu-listening' );
			if ( event.error === 'not-allowed' || event.error === 'service-not-allowed' ) {
				alert( 'Microphone access blocked.\n\nFor voice search to work:\n1. Use HTTPS (https://) not HTTP\n2. Allow microphone permission in browser\n\nNote: Voice search works only in Chrome, Edge, Opera, and Safari.' );
			} else if ( event.error === 'no-speech' ) {
				alert( 'No speech detected. Please try speaking louder.' );
			} else if ( event.error === 'audio-capture' ) {
				alert( 'No microphone found. Please connect a microphone.' );
			}
		};
	}

	// === QR Code per FAQ ===
	function initQRCodes( container ) {
		var items = container.querySelectorAll( FAQ_ITEM );
		items.forEach( function ( item, idx ) {
			var question = item.querySelector( FAQ_QUESTION );
			if ( !question ) return;

			var faqId = 'faq-' + ( idx + 1 );
			item.id = faqId;

			var qrBtn = document.createElement( 'button' );
			qrBtn.className = 'faqsu-qr-btn';
			qrBtn.title = 'Get QR code for this FAQ';
			qrBtn.type = 'button';
			qrBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M3 11h8V3H3v8zm2-6h4v4H5V5zM3 21h8v-8H3v8zm2-6h4v4H5v-4zM13 3v8h8V3h-8zm6 6h-4V5h4v4zM13 13h2v2h-2zM15 15h2v2h-2zM13 17h2v2h-2zM17 13h2v2h-2zM19 15h2v2h-2zM17 17h2v2h-2zM19 19h2v2h-2zM15 19h2v2h-2z"/></svg>';
			question.appendChild( qrBtn );

			qrBtn.addEventListener( 'click', function ( e ) {
				e.stopPropagation();
				showQRModal( faqId, question.textContent.trim().replace( /\s+/g, ' ' ) );
			} );
		} );
	}

	function showQRModal( faqId, questionText ) {
		var faqUrl = window.location.origin + window.location.pathname + '#' + faqId;
		var qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent( faqUrl );

		var modal = document.createElement( 'div' );
		modal.className = 'faqsu-modal';

		var safeQuestion = document.createElement( 'div' );
		safeQuestion.textContent = questionText;
		var escapedQuestion = safeQuestion.innerHTML;

		var safeUrl = document.createElement( 'div' );
		safeUrl.textContent = faqUrl;
		var escapedUrl = safeUrl.innerHTML;

		modal.innerHTML =
			'<div class="faqsu-modal-overlay"></div>' +
			'<div class="faqsu-modal-content">' +
				'<button class="faqsu-modal-close" type="button" aria-label="Close">&times;</button>' +
				'<h3>Share this FAQ</h3>' +
				'<p class="faqsu-modal-question">' + escapedQuestion + '</p>' +
				'<img src="' + qrApiUrl + '" alt="QR Code" class="faqsu-qr-image">' +
				'<p class="faqsu-modal-url">' + escapedUrl + '</p>' +
				'<button class="faqsu-copy-url-btn" type="button">Copy Link</button>' +
			'</div>';
		document.body.appendChild( modal );

		modal.querySelector( '.faqsu-modal-overlay' ).addEventListener( 'click', function () { modal.remove(); } );
		modal.querySelector( '.faqsu-modal-close' ).addEventListener( 'click', function () { modal.remove(); } );

		modal.querySelector( '.faqsu-copy-url-btn' ).addEventListener( 'click', function () {
			var btn = this;
			var setCopied = function () {
				btn.textContent = '✓ Copied!';
				btn.classList.add( 'copied' );
				setTimeout( function () {
					btn.textContent = 'Copy Link';
					btn.classList.remove( 'copied' );
				}, 2000 );
			};
			if ( navigator.clipboard && window.isSecureContext ) {
				navigator.clipboard.writeText( faqUrl ).then( setCopied );
			} else {
				var ta = document.createElement( 'textarea' );
				ta.value = faqUrl;
				ta.style.position = 'fixed';
				ta.style.left = '-9999px';
				document.body.appendChild( ta );
				ta.select();
				try { document.execCommand( 'copy' ); setCopied(); } catch ( e ) {}
				document.body.removeChild( ta );
			}
		} );
	}

	// === Auto-open FAQ from URL hash (for QR sharing) ===
	function handleUrlHash() {
		if ( !window.location.hash ) return;
		var targetId = window.location.hash.substring( 1 );
		var target = document.getElementById( targetId );
		if ( target ) {
			target.classList.add( 'faqsu-targeted' );
			setTimeout( function () {
				target.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			}, 200 );
		}
	}

	// === PDF Export (via new popup window) ===
	function initPDFExport() {
		var btn = document.querySelector( '.faqsu-pdf-btn' );
		if ( !btn ) return;

		btn.addEventListener( 'click', function () {
			var container = document.querySelector( FAQ_CONTAINER );
			if ( !container ) return;

			// Clone container and remove QR buttons
			var clone = container.cloneNode( true );
			var qrBtns = clone.querySelectorAll( '.faqsu-qr-btn' );
			qrBtns.forEach( function ( b ) { b.remove(); } );

			var pageTitle = document.title || 'FAQ Document';
			var siteUrl = window.location.origin;
			var currentDate = new Date().toLocaleDateString( 'en-US', {
				year: 'numeric', month: 'long', day: 'numeric'
			} );

			var html = '<!DOCTYPE html>' +
				'<html><head>' +
				'<meta charset="UTF-8">' +
				'<title>FAQs - ' + pageTitle + '</title>' +
				'<style>' +
					'* { box-sizing: border-box; }' +
					'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding: 32px; color: #333; line-height: 1.5; max-width: 800px; margin: 0 auto; }' +
					'.faqsu-pdf-header { padding-bottom: 16px; margin-bottom: 24px; border-bottom: 2px solid #3858E9; }' +
					'.faqsu-pdf-header h1 { font-size: 26px; margin: 0 0 6px 0; color: #1e1e1e; font-weight: 700; }' +
					'.faqsu-pdf-header p { margin: 0; color: #666; font-size: 13px; }' +
					'#faqsu-faq-list { padding: 0; }' +
					'.faqsu-faq-single { page-break-inside: avoid; margin-bottom: 20px; padding: 16px 20px; background: #f8f9fa; border-left: 4px solid #3858E9; border-radius: 6px; }' +
					'.faqsu-faq-question { font-size: 16px; font-weight: 700; color: #1e1e1e; margin: 0 0 10px 0; padding: 0; }' +
					'.faqsu-faq-answare, .faqsu-faq-answer { color: #555; line-height: 1.6; font-size: 14px; }' +
					'.faqsu-pdf-footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e0e0e0; font-size: 11px; color: #999; text-align: center; }' +
					'@page { margin: 1.5cm; }' +
					'@media print { body { padding: 0; max-width: 100%; } }' +
				'</style>' +
				'</head><body>' +
					'<div class="faqsu-pdf-header">' +
						'<h1>Frequently Asked Questions</h1>' +
						'<p>' + escapeHtml( pageTitle ) + ' &middot; ' + currentDate + '</p>' +
					'</div>' +
					clone.outerHTML +
					'<p class="faqsu-pdf-footer">Generated from ' + escapeHtml( siteUrl ) + '</p>' +
				'</body></html>';

			var printWindow = window.open( '', '_blank', 'width=800,height=600' );
			if ( !printWindow ) {
				alert( 'Please allow popups for this site to download PDF.' );
				return;
			}
			printWindow.document.open();
			printWindow.document.write( html );
			printWindow.document.close();

			printWindow.onload = function () {
				setTimeout( function () {
					printWindow.print();
					setTimeout( function () {
						printWindow.close();
					}, 500 );
				}, 250 );
			};
		} );
	}

	// === Utility: Escape HTML ===
	function escapeHtml( text ) {
		var div = document.createElement( 'div' );
		div.textContent = text;
		return div.innerHTML;
	}

	// === DOM Ready ===
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();