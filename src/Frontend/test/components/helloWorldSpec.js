import React from 'react';
import HelloWorld from '../../components/HelloWorld';
import chai from 'chai';
import jsdom from 'jsdom';
import ReactTestUtils from 'react/lib/ReactTestUtils';
import ReactDOM from 'react-dom';

describe('helloWorld', () => {
  it('renders hello world', () => {
    const result = ReactTestUtils.renderIntoDocument(<HelloWorld />);

    const cmp = ReactDOM.findDOMNode(result);
    chai.expect(cmp._childNodes[0]._nodeValue).to.equal('Hello World!');
    chai.expect(cmp._localName).to.equal('h1');
  });
});
